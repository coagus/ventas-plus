<?php
class BadgeResult
{
    public function getColorCount($count, $hide = '1')
    {
        $color = 'danger';
        $success = 1000;
        $light = 500;
        $warning = 0;

        if ($count > $success) {
            $color = 'success';
        } else if ($count > $light) {
            $color = 'light';
        } else if ($count > $warning) {
            $color = 'warning';
        }

        return $hide == '1' && $count > $light ? ''
            : "<span class='badge text-bg-$color'> $count </span>";
    }
}
?>