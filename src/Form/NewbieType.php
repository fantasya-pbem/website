<?php
declare (strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NewbieType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', TextType::class, [
			'label' => 'Name der Partei'
		]);
		$builder->add('description', TextareaType::class, [
			'label' => 'Beschreibung'
		]);
		$builder->add('race', ChoiceType::class, [
			'label'   => 'Rasse',
			'choices' => [
				'Aquaner'  => 'Aquaner',
				'Elf'      => 'Elf',
				'Halbling' => 'Halbling',
				'Mensch'   => 'Mensch',
				'Ork'      => 'Ork',
				'Troll'    => 'Troll',
				'ZWerg'    => 'Zwerg'
			]
		]);
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
