<?php

namespace rp\util;

use rp\data\event\Event;
use wcf\data\language\Language;
use wcf\data\user\User;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

/**
 * Contains raidplaner-related functions.
 * 
 * @author  Marco Daries
 * @license Raidplaner License <https://daries.dev/licence/raidplaner.txt>
 */
final class RPUtil
{
    /**
     * fake user object with `UTC` set as timezone
     */
    private static ?User $UTCUser = null;

    /**
     * Returns the full day format of a date.
     */
    public static function formatEventFullDay(\DateTime $time, ?Language $language = null): string
    {
        return DateUtil::format($time, Event::DATE_FORMAT, $language, self::getUTCUser());
    }

    /**
     * Formats the points
     */
    public static function formatPoints(float|int $points = 0): string
    {
        return StringUtil::formatDouble(
                $points,
                RP_ROUND_POINTS ? RP_ROUND_POINTS_PRECISION : 2
        );
    }

    /**
     * Returns a fake user object with `UTC` set as timezone.
     */
    private static function getUTCUser(): User
    {
        if (self::$UTCUser === null) {
            self::$UTCUser = new User(null, ['timezone' => 'UTC']);
        }

        return self::$UTCUser;
    }
}
