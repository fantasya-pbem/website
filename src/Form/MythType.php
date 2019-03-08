<?php
declare (strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MythType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('myth', TextType::class,[
			'label' => '...das da lautet:'
		]);
		$builder->add('submit', SubmitType::class, [
			'label' => 'Gerücht verbreiten'
		]);
	}
}
