<?php
declare(strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NoSuspiciousCharacters;
use Symfony\Component\Validator\Constraints\NotBlank;

class MythType extends AbstractType
{
	public function __construct(protected HtmlSanitizerInterface $appMythSanitizer) {
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder->add('myth', TextType::class,[
			'label'         => '...das da lautet:',
			'attr'          => ['autofocus' => true],
			'sanitize_html' => true,
			'sanitizer'     => 'app.myth_sanitizer',
			'constraints'   => [
				new Length(
					min: 10,  minMessage: 'Bitte gib einen etwas längeren Text eun.',
					max: 255, maxMessage: 'Der Text ist zu lang, bitte verwende nicht mehr als 250 Zeichen.'
				),
				new NoSuspiciousCharacters(),
				new NotBlank(
					message: 'Bitte gib nur den reinen Text ein, verwende kein HTML.'
				)
			]
		]);
		$builder->add('submit', SubmitType::class, [
			'label' => 'Gerücht verbreiten'
		]);
	}
}
