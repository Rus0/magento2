<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Msrp\Test\Unit\Pricing;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type as Type;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedType;
use Magento\Msrp\Pricing\MsrpPriceCalculator;
use Magento\MsrpGroupedProduct\Pricing\MsrpPriceCalculator as MsrpGroupedCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MsrpPriceCalculatorTest extends TestCase
{
    /**
     * Test getMrspPriceValue() with the data provider below
     *
     * @param array $msrpPriceCalculators
     * @param Product $productMock
     * @param float $expected
     * @dataProvider getMsrpPriceValueDataProvider
     */
    public function testGetMsrpPriceValue($msrpPriceCalculators, $productMock, $expected)
    {
        $objectManager = new ObjectManager($this);
        $pricing = $objectManager->getObject(MsrpPriceCalculator::class,
            [
                'msrpPriceCalculators' => $msrpPriceCalculators
            ]
        );

        $this->assertEquals($expected, $pricing->getMsrpPriceValue($productMock));
    }

    /**
     * Data Provider for test getMrspPriceValue()
     *
     * @return array
     */
    public function getMsrpPriceValueDataProvider()
    {
        return [
            'Get Mrsp Price with grouped product and price calculator is also grouped product type' => [
                [
                    [
                        'productType' => GroupedType::TYPE_CODE,
                        'priceCalculator' => $this->createPriceCalculatorMock(
                            MsrpGroupedCalculator::class, 23.50)
                    ]
                ],
                $this->createProductMock(GroupedType::TYPE_CODE, 0),
                23.50
            ],
            'Get Mrsp Price with simple product and price calculator is grouped product type' => [
                [
                    [
                        'productType' => GroupedType::TYPE_CODE,
                        'priceCalculator' => $this->createPriceCalculatorMock(
                            MsrpGroupedCalculator::class, 0)
                    ]
                ],
                $this->createProductMock(Type::TYPE_SIMPLE, 24.88),
                24.88
            ]
        ];
    }

    /**
     * Create Price Calculator Mock
     *
     * @param string $class
     * @param float $msrpPriceValue
     * @return MockObject
     */
    private function createPriceCalculatorMock($class, $msrpPriceValue)
    {
        $priceCalculatorMock = $this->createMock($class);
        $priceCalculatorMock->expects($this->any())->method('getMsrpPriceValue')->willReturn($msrpPriceValue);
        return $priceCalculatorMock;
    }

    /**
     * Create Product Mock
     *
     * @param string $typeId
     * @param float $msrp
     * @return MockObject
     */
    private function createProductMock($typeId, $msrp)
    {
        $productMock = $this->createPartialMock(Product::class, ['getTypeId', 'getMsrp']);
        $productMock->expects($this->any())->method('getTypeId')->willReturn($typeId);
        $productMock->expects($this->any())->method('getMsrp')->willReturn($msrp);
        return $productMock;
    }
}
