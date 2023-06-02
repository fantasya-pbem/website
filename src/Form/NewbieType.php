<?php
declare(strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use App\Game\Race;

class NewbieType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder->add('name', TextType::class, [
			'label' => 'Name der Partei',
			'attr'  => ['autofocus' => true, 'tabindex' => 1]
		]);
		$builder->add('description', TextareaType::class, [
			'label'    => 'Beschreibung',
			'required' => false,
			'attr'     => ['tabindex' => 2]
		]);
		$builder->add('race', ChoiceType::class, [
			'label'   => 'Rasse',
			'choices' => array_combine(Race::all(), Race::all()),
			'attr'    => ['tabindex' => 4]
		]);
		$builder->add('submit', SubmitType::class, [
			'label' => 'Partei erstellen',
			'attr'  => ['tabindex' => 3]
		]);
	}
}
