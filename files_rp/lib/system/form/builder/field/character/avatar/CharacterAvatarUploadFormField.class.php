<?php

namespace rp\system\form\builder\field\character\avatar;

use wcf\data\IStorableObject;
use wcf\system\file\upload\UploadFile;
use wcf\system\form\builder\field\UploadFormField;
use wcf\util\ImageUtil;


/**
 * Implementation of a form field for to upload character avatars.
 *
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
class CharacterAvatarUploadFormField extends UploadFormField
{

    /**
     * @inheritDoc
     *
     * @throws \InvalidArgumentException    if the getter for the value provides invalid values
     */
    public function updatedObject(array $data, IStorableObject $object, $loadValues = true): CharacterAvatarUploadFormField
    {
        if ($loadValues) {
            // first check, whether an getter for the field exists
            if (\method_exists($object, 'get' . \ucfirst($this->getObjectProperty()) . 'UploadFileLocations')) {
                $value = \call_user_func([
                    $object,
                    'get' . \ucfirst($this->getObjectProperty()) . 'UploadFileLocations',
                ]);
                $method = "method '" . \get_class($object) . "::get" . \ucfirst($this->getObjectProperty()) . "UploadFileLocations()'";
            } elseif (\method_exists($object, 'get' . \ucfirst($this->getObjectProperty()))) {
                $value = \call_user_func([$object, 'get' . \ucfirst($this->getObjectProperty())]);
                $method = "method '" . \get_class($object) . "::get" . \ucfirst($this->getObjectProperty()) . "()'";
            } else {
                $value = $data[$this->getObjectProperty()];
                $method = "variable '" . \get_class($object) . "::$" . $this->getObjectProperty() . "'";
            }

            if ($value !== null) {
                if (\is_array($value)) {
                    $value = \array_map(function ($v) use ($method) {
                        if (!\is_string($v) || !\file_exists($v)) {
                            throw new \InvalidArgumentException(
                                    "The " . $method . " must return an array of strings with the file locations."
                            );
                        }

                        return new UploadFile(
                        $v,
                        \basename($v),
                        ImageUtil::isImage($v, \basename($v), $this->svgImageAllowed()),
                        true,
                        $this->svgImageAllowed()
                        );
                    }, $value);

                    $this->value($value);
                } else {
                    throw new \InvalidArgumentException(
                            "The " . $method . " must return an array of strings with the file locations."
                    );
                }
            }
        }

        return $this;
    }
}
