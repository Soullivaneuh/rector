<?php

namespace Rector\Php\Tests\Rector\ClassMethod\ParamAndReturnScalarTypehintsRector\Integration\Dunglas;

interface BarInterface
{
    /**
     * @param array $a
     * @param int   $b
     *
     * @return float
     */
    public function baz(array $a, $b);
}
?>
-----
<?php

namespace Rector\Php\Tests\Rector\ClassMethod\ParamAndReturnScalarTypehintsRector\Integration\Dunglas;

interface BarInterface
{
    /**
     * @param array $a
     * @param int   $b
     *
     * @return float
     */
    public function baz(array $a, int $b): float;
}
?>
