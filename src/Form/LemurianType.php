<?php
declare (strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use App\Game\Race;

class LemurianType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', TextType::class, [
			'label' => 'Name der Partei'
		]);
		$builder->add('description', TextareaType::class, [
			'label' => 'Beschreibung'
		]);
		$builder->add('race', ChoiceType::class, [
			'label'   => 'Rasse',
			'choices' => array_combine(Race::all(), Race::all())
		]);
		$builder->add('submit', SubmitType::class, [
			'label' => 'Partei erstellen'
		]);
	}
}
