<?php

namespace TeamChallengeApps\Distance\Formatter;

use TeamChallengeApps\Distance\DistanceValue;

interface DistanceFormatter {

    public function format(DistanceValue $distance, array $options = []): mixed;

}
