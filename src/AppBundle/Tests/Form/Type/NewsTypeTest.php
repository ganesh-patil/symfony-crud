<?php
namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\NewsType;
//use AppBundle\Model\TestObject;
use Symfony\Component\Form\Test\TypeTestCase;

class NewsTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
        'test' => 'test',
        'test2' => 'test2',
        );

        $type = new NewsType();
        $form = $this->factory->create($type);

//        $object = TestObject::fromArray($formData);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
//        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}