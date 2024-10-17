<?php

namespace strtob\yii2Traits;  // Adjust the namespace according to your application structure

use Yii;

/**
 * Trait ValidityTrait
 *
 * This trait provides functionality to determine the validity stage of a date range
 * based on two date properties: valid_from and valid_until. It categorizes the state
 * of validity into three stages and provides corresponding messages and icons.
 * 
 * @package strtob\yii2Traits
 */
trait ValidityTrait
{
    // Define stages as constants
    const STAGE_ACTIVE = 1;                  // Active (within range or no valid range defined)
    const STAGE_INACTIVE_GONE = 2;           // Inactive (valid until in the past)
    const STAGE_INACTIVE_FUTURE_START = 3;   // Valid from in the future

    /**
     * Array of icons corresponding to each validity stage.
     * 
     * @var string[]
     */
    private $icons = [
        self::STAGE_ACTIVE => 'fas fa-check-circle text-success',               // Icon for active
        self::STAGE_INACTIVE_GONE => 'fas fa-times-circle text-danger',        // Icon for inactive (gone)
        self::STAGE_INACTIVE_FUTURE_START => 'fas fa-clock text-warning',       // Icon for future validity
    ];

    /**
     * The attribute name for the valid from date.
     * 
     * @var string
     */
    private $validFromAttribute = 'valid_from';

    /**
     * The attribute name for the valid until date.
     * 
     * @var string
     */
    private $validUntilAttribute = 'valid_until';

    /**
     * Sets the attribute names for valid from and valid until dates.
     *
     * @param string $validFrom The name of the valid from attribute.
     * @param string $validUntil The name of the valid until attribute.
     */
    public function setValidityAttributes($validFrom, $validUntil)
    {
        $this->validFromAttribute = $validFrom;
        $this->validUntilAttribute = $validUntil;
    }

    /**
     * Determines the validity stage of the date range.
     *
     * This method analyzes the valid_from and valid_until dates to determine the current
     * validity status. It returns an object containing the stage number, a descriptive
     * message, the relative time for the dates, and the corresponding icon for the stage.
     *
     * @return object An object containing the following properties:
     * - stage: int The stage number representing the validity status.
     * - message: string A user-friendly message describing the validity status.
     * - relative_time: string A relative time string for the valid_until or valid_from date.
     * - icon: string The font-awesome icon class associated with the validity stage.
     */
    public function getValidityStage()
    {
        // Get the current date
        $now = new \DateTime();

        // Check valid_from and valid_until dates using dynamic attribute names
        $validFrom = $this->{$this->validFromAttribute} ? new \DateTime($this->{$this->validFromAttribute}) : null;
        $validUntil = $this->{$this->validUntilAttribute} ? new \DateTime($this->{$this->validUntilAttribute}) : null;

        // Prepare response object
        $response = (object)[
            'stage' => null,
            'message' => '',
            'relative_time' => '',
            'icon' => '',  // Placeholder for icon
        ];

        // Determine the stage
        if ($validFrom === null && $validUntil === null) {
            // Case 1: Both dates are null (active)
            $response->stage = self::STAGE_ACTIVE;
            $response->message = Yii::t('app', 'active'); // Kept original text
            $response->icon = $this->icons[self::STAGE_ACTIVE];
        } elseif ($validUntil !== null && $validUntil < $now) {
            // Case 2: valid_until is in the past (inactive)
            $response->stage = self::STAGE_INACTIVE_GONE;
            $response->message = Yii::t('app', 'inactive'); // Kept original text
            $response->relative_time = Yii::$app->formatter->asRelativeTime($validUntil);
            $response->icon = $this->icons[self::STAGE_INACTIVE_GONE];
        } elseif ($validFrom !== null && $validFrom > $now) {
            // Case 3: valid_from is in the future
            $response->stage = self::STAGE_INACTIVE_FUTURE_START;
            $response->message = Yii::t('app', 'start in the future'); // Kept original text
            $response->relative_time = Yii::$app->formatter->asRelativeTime($validFrom);
            $response->icon = $this->icons[self::STAGE_INACTIVE_FUTURE_START];
        } else {
            // Case 4: valid_from is in the past and valid_until is in the future (active)
            $response->stage = self::STAGE_ACTIVE;
            $response->message = Yii::t('app', 'active'); // Kept original text
            $response->relative_time = Yii::$app->formatter->asRelativeTime($validUntil);
            $response->icon = $this->icons[self::STAGE_ACTIVE];
        }

        return $response;
    }
}
