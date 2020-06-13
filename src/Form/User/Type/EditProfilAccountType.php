<?php

declare(strict_types=1);

namespace App\Form\User\Type;

use App\Entity\Category;
use App\Entity\User;
use App\EventSubscriber\UserSubscriber;
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
    public CategoryRepository $categoryRepository;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', TextType::class, [
                'attr' => [
                    'autocomplete_url' => $this->router->generate('category_autocomplete'),
                    'label' => 'Categories (autocomplete)',
                ],
                'label' => false,
                'block_prefix' => 'autocomplete',
            ]);

        $builder
            ->get('categories')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {
                    return implode(',', array_map(function (Category $categoryEntity) {
                        return $categoryEntity->getTitle();
                    }, $tagsAsArray->toArray()));
                },
                function ($tagsAsString) {
                    if (null !== $tagsAsString) {
                        $categories = array_map(function (string $categoryTitle) {
                            return $this->categoryRepository->findOneBy(['title' => $categoryTitle]) ?? null;
                        }, explode(',', $tagsAsString));

                        $categories = array_filter($categories, function ($category) {
                            return null !== $category;
                        });
                    }

                    return $categories ?? [];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
