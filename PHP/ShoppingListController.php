<?php
namespace App\Controller;

use App\Entity\ShoppingList;
use App\Form\ShoppingListType;
use App\Repository\ProductsStoreRepository;
use App\Repository\ShoppingListRepository;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShoppingListController extends AbstractController
{
  
  private $shoppingListRepo;
  private $productRepo;
  
  public function __construct(ProductsStoreRepository $productRepo,
                              ShoppingListRepository $shoppingListRepo){
    $this->shoppingListRepo = $shoppingListRepo;
    $this->productRepo = $productRepo;
  }
  

  #[Route('/list', 'lista_zakupow')]

  public function list(): Response {

    $shoppingList = $this->shoppingListRepo->getShoppingList();

    return $this->render('shopping_list/index.html.twig',[
            'shopping_list' => $shoppingList
    ]);
  }


  #[Route('/list/kupione', 'add_purchase')]

  public function addPurchase(Request $request): Response {
    if(!empty($request->get('name'))){
      $productName = $request->get('name');
      
      if(!empty($request->get('quantity'))){
        $addedQuantity = $request->get('quantity');
        $product = $this->productRepo->findOneBy(['name' => $productName]);
        $newQuantity = $product->getQuantity() + $addedQuantity;
        
        $product->setQuantity($newQuantity);
        $this->productRepo->add($product, true);
        $this->addFlash('success', 'Produkt dodano do magazynu.');
      
      }else{
        $product = $this->shoppingListRepo->findOneBy(['name' => $productName]);
        
        $this->shoppingListRepo->remove($product, true);
        $this->addFlash('success', 'Produkt usunięto z listy.');
      }
    }
    
    else{
      $this->addFlash('error', 'Coś poszło nie tak!');
    }

    return $this->redirectToRoute('lista_zakupow');
  }


  #[Route('/list/dodaj', 'add_to_shopping_list')]

  public function addToShoppingList(Request $request): Response {
    
    $product = new ShoppingList;
    $form = $this->createForm(ShoppingListType::class, $product, ['action' => $this->generateUrl('add_to_shopping_list')]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      
      if($form->get('to_store')->getData()){
        $formData = $form->getData()->getAll();
        return $this->forward('App\Controller\ProductController::addNewProduct',[
                    'formData' => $formData,
        ]);
      }

      $formData = $form->getData();
      $product->setName($formData->getName())
              ->setShoppingQuantity($formData->getShoppingQuantity())
              ->setUnit($formData->getUnit())
              ->setCategory($formData->getCategory());
      $this->shoppingListRepo->add($product, true);
      $this->addFlash('success', 'Produkt '.$product->getName().' został dodany!');
      
      return $this->redirectToRoute('home_page');
    }


    return $this->render('shopping_list/add_to_shopping_list.html.twig',[
                        'form' => $form->createView(),
                  'categories' => $this->productRepo->getCategories(),
    ]);
  }
}