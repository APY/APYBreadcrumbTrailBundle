<?php

/*
 * This file is part of the APYBreadcrumbTrailBundleBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\BreadcrumbTrailBundle\BreadcrumbTrail;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Breadcrumb
{
    /**
     * @var string Title of the breadcrumb
     */
    public $title;

    /**
     * @var string Url of the breadcrumb
     */
    public $url;

    /**
     * Constructor.
     *
     * @param string $title Title of the breadcrumb
     * @param string $url Url of the breadcrumb
     */
    public function __construct($title, $url = null)
    {
        $this->title = $title;
        $this->url = $url;
    }
}
