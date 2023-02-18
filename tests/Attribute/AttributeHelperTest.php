<?php

namespace Attribute;

use Ict\ApiOneEndpoint\Attribute\AttributeHelper;
use Ict\ApiOneEndpoint\Model\Attribute\IsBackground;
use PHPUnit\Framework\TestCase;

class AttributeHelperTest extends TestCase
{
    public function testNoAttribute()
    {
        $attributeHelper = new AttributeHelper();
        $attr = $attributeHelper->getAttr(new \stdClass(), IsBackground::class);

        $this->assertNull($attr);
    }

    public function testIsBackgroundAttribute()
    {
        $simpleOperation = new SimpleOperation();
        $attributeHelper = new AttributeHelper();
        $attr = $attributeHelper->getAttr($simpleOperation, IsBackground::class);
        $this->assertInstanceOf(IsBackground::class, $attr);
        $this->assertEquals(300, $attr->delay);
    }
}

#[IsBackground(delay: 300)]
class SimpleOperation {

}
