<?php

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Litefyr\Migrations\RenameNodeTypesMigration;

/**
 * Rename Slider content
 */
class Version20240726000000 extends RenameNodeTypesMigration
{
    public array $nodeTypes = [
        'Litefyr.Slider:Content.Headline' => 'Litefyr.Integration:Content.Headline',
        'Litefyr.Slider:Content.Image' => 'Litefyr.Integration:Content.Image',
        'Litefyr.Slider:Content.Text' => 'Litefyr.Integration:Content.Text',
        'Litefyr.Slider:Content.Video' => 'Jonnitto.PrettyEmbedVideo:Content.Video',
        'Litefyr.Slider:Content.VideoPlatforms' => 'Jonnitto.PrettyEmbedVideoPlatforms:Content.Video',
    ];
}
