<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 12/10/18
 * Time: 15:00
 */

namespace Framework\Twig;

use Framework\Session\FlashService;

/**
 * Class FlashExtension
 * @package Framework\Twig
 */
class FlashExtension extends \Twig_Extension
{

    /**
     * @var FlashService
     */
    private $flashService;


    /**
     * FlashExtension constructor.
     * @param FlashService $flashService
     */
    public function __construct(FlashService $flashService)
    {

        $this->flashService = $flashService;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('flash', [$this, 'getFlash'])
        ];
    }

    /**
     * @param $type
     * @return null|string
     */
    public function getFlash($type): ?string
    {
        return $this->flashService->get($type);
    }
}
