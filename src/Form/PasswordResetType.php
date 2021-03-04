<?php
declare (strict_types = 1);
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PasswordResetType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', TextType::class,[
			'label' => 'Benutzername'
		]);
		$builder->add('email', EmailType::class, [
			'label' => 'E-Mail-Adresse'
		]);
		$builder->add('submit', SubmitType::class, [
			'label' => 'Passwort anfordern'
		]);
	}
}
