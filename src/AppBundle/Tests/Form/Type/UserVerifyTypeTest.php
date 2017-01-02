<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\UserVerifyType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @covers AppBundle\Form\UserVerifyType
 */
class UserVerifyTypeTest extends TypeTestCase
{



    // check configure options
    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $type = new UserVerifyType();

        $type->configureOptions($resolver);
        $this->assertTrue($resolver->isDefined('data_class'));
        $this->assertTrue($resolver->isDefined('validation_groups'));
    }

    // test form submitted data.
    public function testSubmitValidData()
    {
        $formData = array(
            'plainPassword' => 'test',

        );

        $type = new UserVerifyType();
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
