<?php
/**
 * Created by PhpStorm.
 * User: ubuguy
 * Date: 16/10/18
 * Time: 12:17
 */

namespace App\Admin;

interface AdminWidgetInterface
{

    public function render(): string;

    public function renderMenu(): string;
}
