<?php
declare (strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class NewbieType extends LemurianType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);
		$builder->add('wood', IntegerType::class, [
			'label' => 'Holz',
			'attr' => ['min' => 0, 'max' => 90]
		]);
		$builder->add('stone', IntegerType::class, [
			'label' => 'Stein',
			'attr' => ['min' => 0, 'max' => 90]
		]);
		$builder->add('iron', IntegerType::class, [
			'label' => 'Eisen',
			'attr' => ['min' => 0, 'max' => 90]
		]);
		$builder->add('submit', SubmitType::class, [
			'label' => 'Partei erstellen'
		]);
	}
}
