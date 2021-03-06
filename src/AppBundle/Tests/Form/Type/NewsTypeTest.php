<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\NewsType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @covers AppBundle\Form\NewsType
 */
class NewsTypeTest extends TypeTestCase
{

    // get blocl prefix
    public function testGetBlockPrefix()
    {
        $type = new NewsType();

        $this->assertEquals('appbundle_news', $type->getBlockPrefix());
    }


    // check configure options
    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $type = new NewsType();

        $type->configureOptions($resolver);
        $this->assertTrue($resolver->isDefined('data_class'));
    }

    // test form submitted data.
    public function testSubmitValidData()
    {
        $formData = array(
            'title' => 'foo',
            'description' => 'test descirption',
        );

        $type = new NewsType();
        $form = $this->factory->create($type);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
