<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\UserResetType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @covers AppBundle\Form\UserResetType
 */
class UserResetTypeTest extends TypeTestCase
{



    // check configure options
    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $type = new UserResetType();

        $type->configureOptions($resolver);
        $this->assertTrue($resolver->isDefined('data_class'));
    }

    // test form submitted data.
    public function testSubmitValidData()
    {
        $formData = array(
            'email' => 'testemial@mail.com',
        );

        $type = new UserResetType();
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
