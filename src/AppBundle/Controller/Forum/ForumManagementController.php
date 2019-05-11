<?php 

namespace AppBundle\Controller\Forum;

use AppBundle\Entity\ForumCategories;
use AppBundle\Entity\ForumSubcategories;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ForumManagementController extends Controller {

    /**
     * @Route("/forum/management/", name="forum_management")
     */
    public function forumManagementAction() {
        
        $categories = $this->getDoctrine()->getRepository("AppBundle:ForumCategories")->findAll();

        return $this->render("forum/forum-management.html.twig",array(
            'categories' => $categories
        ));
    }

    /**
     * @Route("/forum/management/category/new/", name="forum_management_category_new")
     */
    public function newCategoryAction(Request $request) {

        $forumCategories = new ForumCategories;

        $form = $this->createFormBuilder($forumCategories)
                ->add('name', TextType::class,
                    array(
                        'label' => 'Nume',
                        'attr' =>
                        array(
                            'class' => 'form-control'
                        )
                    )
                )
                ->add('save', SubmitType::class,
                    array(
                        'label' => 'Adauga',
                        'attr' =>
                        array(
                            'class' => 'btn btn-primary'
                        )
                    )
                )
                ->getForm();
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $name = $form['name']->getData();

            $forumCategories->setName($name);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($forumCategories);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Categorie adaugata!'
            );

            return $this->redirect($request->getUri());

        }

        return $this->render("forum/category-management.html.twig",array(
            'pageTitle' => 'Adauga categorie noua',
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/forum/management/category/edit/{category_id}", name="forum_management_category_edit")
     */
    public function editCategoryAction($category_id, Request $request) {

        $forumCategory = $this->getDoctrine()->getRepository('AppBundle:ForumCategories')->find($category_id);

        $form = $this->createFormBuilder($forumCategory)
                ->add('name', TextType::class,
                    array(
                        'label' => 'Nume',
                        'attr' =>
                        array(
                            'class' => 'form-control'
                        )
                    )
                )
                ->add('save', SubmitType::class,
                    array(
                        'label' => 'Salveaza',
                        'attr' =>
                        array(
                            'class' => 'btn btn-success'
                        )
                    )
                )
                ->getForm();
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $forumCategory = $entityManager->getRepository("AppBundle:ForumCategories")->find($category_id);

            $forumCategory->setName($name);

            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Modificarile au fost salvate cu success!'
            );

            return $this->redirect($request->getUri());
        }

        return $this->render("forum/category-management.html.twig", array(
            'pageTitle' => 'Editeaza categoria',
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/forum/management/category/delete/{category_id}", name="forum_management_category_delete")
     */
    public function deleteCategoryAction($category_id, Request $request) {

        $entityManager = $this->getDoctrine()->getManager();
        $forumCategory = $entityManager->getRepository("AppBundle:ForumCategories")->find($category_id);

        $entityManager->remove($forumCategory);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Categoria a fost stearsa'
        );

        return $this->redirectToRoute("forum_management");
    }

    /**
     * @Route("/forum/management/category/{category_id}/subcategory/new/", name="forum_management_subcategory_new")
     */
    public function newSubcategoryAction($category_id, Request $request) {

        $forumCategory = $this->getDoctrine()->getRepository("AppBundle:ForumCategories")->find($category_id);
        $forumSubcategory = new ForumSubcategories;

        $form = $this->createFormBuilder($forumSubcategory)
                ->add('name', TextType::class,
                    array(
                        'label' => 'Nume',
                        'attr' =>
                            array(
                                'class' => 'form-control'
                            )
                        )
                    )
                ->add('description', TextareaType::class,
                    array(
                        'label' => 'Descriere',
                        'attr' =>
                            array(
                                'class' => 'form-control'
                            )
                        )   
                    )
                ->add('save', SubmitType::class,
                    array(
                        'label' => 'Adauga',
                        'attr' =>
                        array(
                            'class' => 'btn btn-primary'
                        )
                    )
                )
                ->getForm();
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $name = $form['name']->getData();
            $description = $form['description']->getData();

            $forumSubcategory->setName($name);
            $forumSubcategory->setDescription($description);
            $forumSubcategory->setCategory($forumCategory);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($forumSubcategory);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Subcategorie adaugata!'
            );

            return $this->redirect($request->getUri());

        }

        return $this->render("forum/subcategory-management.html.twig",array(
            'pageTitle' => $forumCategory->getName().' > Adauga categorie noua',
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/forum/management/category/subcategory/edit/{subcategory_id}", name="forum_management_subcategory_edit")
     */
    public function editSubcategoryAction($subcategory_id, Request $request) {

        $forumSubcategory = $this->getDoctrine()->getRepository('AppBundle:ForumSubcategories')->find($subcategory_id);

        $form = $this->createFormBuilder($forumSubcategory)
                ->add('name', TextType::class,
                    array(
                        'label' => 'Nume',
                        'attr' =>
                        array(
                            'class' => 'form-control'
                        )
                    )
                )
                ->add('description', TextareaType::class,
                    array(
                        'label' => 'Descriere',
                        'attr' =>
                            array(
                                'class' => 'form-control'
                            )
                        )   
                    )
                ->add('save', SubmitType::class,
                    array(
                        'label' => 'Salveaza',
                        'attr' =>
                        array(
                            'class' => 'btn btn-success'
                        )
                    )
                )
                ->getForm();
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $name = $form['name']->getData();
            $description = $form['description']->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $forumSubcategory = $entityManager->getRepository("AppBundle:ForumSubcategories")->find($subcategory_id);

            $forumSubcategory->setName($name);
            $forumSubcategory->setDescription($description);

            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Modificarile au fost salvate cu success!'
            );

            return $this->redirect($request->getUri());
        }

        return $this->render("forum/category-management.html.twig", array(
            'pageTitle' => 'Editeaza categoria',
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/forum/management/category/subcategory/delete/{subcategory_id}", name="forum_management_subcategory_delete")
     */
    public function deleteSubcategoryAction($subcategory_id, Request $request) {

        $entityManager = $this->getDoctrine()->getManager();
        $forumSubcategory = $entityManager->getRepository("AppBundle:ForumSubcategories")->find($subcategory_id);

        $entityManager->remove($forumSubcategory);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Subcategoria a fost stearsa'
        );

        return $this->redirectToRoute("forum_management");
    }
}