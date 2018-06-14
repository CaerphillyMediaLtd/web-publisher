<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Routing;

use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\AuthorMedia;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MediaRouter extends Router implements VersatileGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if (null === $item = $this->getItem($name)) {
            return;
        }

        $routeName = 'swp_media_get';
        if ($name instanceof Meta && $name->getValues() instanceof AuthorMedia) {
            $routeName = 'swp_author_media_get';
        }
        dump($routeName);
        $parameters['mediaId'] = $item->getAssetId();
        $parameters['extension'] = $item->getFileExtension();

        return parent::generate($routeName, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name instanceof Meta && (
            $name->getValues() instanceof ArticleMedia ||
            $name->getValues() instanceof ImageRendition ||
            $name->getValues() instanceof AuthorMedia
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDebugMessage($name, array $parameters = array())
    {
        return 'Route for media '.$name->getValues()->getId().' not found';
    }

    private function getItem($name)
    {
        $values = $name->getValues();
        if ($name->getValues() instanceof ImageRendition) {
            return $name->getValues()->getImage();
        }

        if ($values->getImage() instanceof ImageInterface) {
            return $values->getImage();
        } elseif ($values->getFile() instanceof FileInterface) {
            return $values->getFile();
        }

        return;
    }
}
