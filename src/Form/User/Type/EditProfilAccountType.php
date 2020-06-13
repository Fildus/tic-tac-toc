<?php

declare(strict_types=1);

namespace App\Form\User\Type;

use App\Entity\Category;
use App\Entity\User;
use App\EventSubscriber\UserSubscriber;
use App\Form\DataTransformer\CategoryToCollectionTransfomer;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class EditProfilAccountType extends AbstractType
{
    /** @required */
    public UserSubscriber $subscriber;
    /** @required */
    public RouterInterface $router;
    /** @required */
    public UserRepository $userRepository;
    /** @required */
    public CategoryToCollectionTransfomer $categoryToCollectionTransfomer;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', TextType::class, [
                'attr' => [
                    'autocomplete_url' => $this->router->generate('category_autocomplete'),
                    'label' => 'Categories',
                ],
                'label' => false,
                'block_prefix' => 'autocomplete',
            ]);

        $builder
            ->get('categories')
            ->addModelTransformer($this->categoryToCollectionTransfomer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
