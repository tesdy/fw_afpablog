<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 18/10/18
 * Time: 15:25
 */

namespace Framework;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class Upload
 * @package Framework
 */
class Upload
{

    /**
     * @var null|string
     */
    protected $path;
    /**
     * @var
     */
    protected $formats;

    /**
     * Upload constructor.
     * @param null|string $path
     */
    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    /**
     * @param UploadedFileInterface $file
     * @param null|string $oldFile
     * @return null|string
     */
    public function upload(UploadedFileInterface $file, ?string $oldFile = null): ?string
    {
        if ($file->getError() === UPLOAD_ERR_OK) {
            $this->delete($oldFile);
            $targetPath = $this->addCopySuffix($this->path . DIRECTORY_SEPARATOR . $file->getClientFilename());
            $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
            if (!file_exists($dirname)) {
                if (!mkdir($dirname, 777, true) && !is_dir($dirname)) {
                    throw new \RuntimeException(sprintf('Le repertoire "%s" n\'a pas été créé', $dirname));
                }
            }
            $file->moveTo($targetPath);
            $this->generateFormats($targetPath);
            return pathinfo($targetPath)['basename'];
        }
        if ($oldFile) {
            return $oldFile;
        }
        return null;
    }

    private function addCopySuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }
        return $targetPath;
    }

    public function delete(?string $oldfile): void
    {
        if ($oldfile) {
            $oldfile = $this->path . DIRECTORY_SEPARATOR . $oldfile;
            if (file_exists($oldfile)) {
                unlink($oldfile);
            }
            foreach ($this->formats as $format => $_) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldfile, $format);
                if (file_exists($oldFileWithFormat)) {
                    unlink($oldFileWithFormat);
                }
            }
        }
    }

    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        return $info['dirname'] . DIRECTORY_SEPARATOR .
            $info['filename'] . '_' . $suffix . '.' . $info['extension'];
    }

    private function generateFormats(string $targetPath)
    {
        foreach ($this->formats as $format => $size) {
            $manager = new ImageManager(['driver' => 'gd']);
            $destination = $this->getPathWithSuffix($targetPath, $format);
            [$width, $height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($destination);
        }
    }
}
