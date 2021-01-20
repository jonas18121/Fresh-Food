<?php

namespace App\Form\Type;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserChoiceType extends AbstractType implements DataTransformerInterface 
{

    protected $em;
    protected $url;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $url)
    {
        $this->em = $em;
        $this->url = $url;
    }

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
        parent::buildForm($builder, $options);
    }

    public function buildView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options): void
    {
        $choices = [];
        /** @var ?User $user */
        $user = $form->getData();
        if ($user instanceof User) {
            $choices = [new ChoiceView($user, (string)$user->getId(), $user->getUsername())];
        }
        $view->vars['choice_translation_domain'] = false;
        $view->vars['expanded'] = false;
        $view->vars['placeholder'] = null;
        $view->vars['placeholder_in_choices'] = false;
        $view->vars['multiple'] = false;
        $view->vars['preferred_choices'] = [];
        $view->vars['value'] = $user ? $user->getId() : 0;
        $view->vars['choices'] = $choices;
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'attr' => [
                'is' => 'user-select',
                'endpoint' => $this->url->generate('admin_user_search')
            ]
        ]);
    }
    
    public function getBlockPrefix(): string
    {
        return 'choice';
    }

    /**
     * @param ?User $user
     */
    public function transform($user): string
    {
        return $user === null ? '' : (string)$user->getId();
    }

    /**
     * @param int $userId
     */
    public function reverseTransform($userId): User {
        return $this->em->getRepository(User::class)->find($userId);
    }
}
