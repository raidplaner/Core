<?php

namespace rp\system\event;

use rp\data\event\Event;
use wcf\system\form\builder\IFormDocument;
use wcf\system\style\FontAwesomeIcon;

/**
 * Default interface for event controllers.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
interface IEventController
{

    /**
     * Creates the form object.
     * 
     * This is the method that is intended to be overwritten by child classes
     * to add the form containers and fields.
     */
    public function createForm(IFormDocument $form): void;

    /**
     * Returns the template in the main section of the event.
     */
    public function getContent(): string;

    /**
     * Returns the database object of this event.
     */
    public function getEvent(): ?Event;

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(?int $size = null): string;

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string;

    /**
     * Returns the moderation template name for this event object type.
     */
    public function getModerationTemplate(): string;

    /**
     * Returns the object type name.
     */
    public function getObjectTypeName(): string;

    /**
     * Returns the title of the event.
     */
    public function getTitle(): string;

    /**
     * Returns true if you participate in the event, false otherwise.
     */
    public function hasLogin(): bool;

    /**
     * Checks whether time for this event is expired
     */
    public function isExpired(): bool;

    /**
     * Returns the data of the form which should be saved.
     */
    public function saveForm(array $formData): array;

    /**
     * Sets the database object of this event.
     */
    public function setEvent(Event $event): void;

    /**
     * Sets the form data based on the form object.
     */
    public function setFormObjectData(IFormDocument $form, array $fields = []): void;

    /**
     * Returns `true` if the position matches the event nodes position present in 
     * the controller, otherwise `false`.
     */
    public function showEventNodes(string $position): bool;
}
