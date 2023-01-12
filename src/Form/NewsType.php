<?php
declare(strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('title', TextType::class,[
			'label' => 'Titel'
		]);
		$builder->add('content', TextareaType::class,[
			'label' => 'Nachricht (HTML wird unterstützt)',
			'attr'  => ['rows' => 10]
		]);
		$builder->add('submit', SubmitType::class, [
			'label' => 'News erstellen'
		]);
	}
}
