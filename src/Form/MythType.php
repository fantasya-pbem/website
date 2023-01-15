<?php
declare(strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MythType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('myth', TextType::class,[
			'label' => '...das da lautet:',
			'attr'  => ['autofocus' => true]
		]);
		$builder->add('submit', SubmitType::class, [
			'label' => 'Gerücht verbreiten'
		]);
	}
}
