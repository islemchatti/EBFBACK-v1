<?php


namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * @Route("/api")
 */
class ArticleController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/articles/{id}",
     *     name = "getArticle",
     *     requirements = {"id"="\d+"}
     *     )
     * @Rest\View(
     *     statusCode=200,
     * )
     */
    public function getArticle(Article $article){
    return $article;
}

    /**
     * @Rest\Get(
     *     path = "/articles/carousel",
     *     name = "getArticlesCarousel"
     *     )
     * @Rest\View(
     *     statusCode=200,
     * )
     */
    public function getArticlesCarousel(ArticleRepository $repo){
        //$products=$repo->findBy([],['id'=>$order]);
        //$products=$repo->findAll();
        $order="desc";
        $limit=4;
        $articles=$repo->findcarouselArticles($order,$limit);

        return $articles;
    }

    /**
     * @Rest\Get(
     *     path = "/articles/list",
     *     name = "getArticlesList"
     *     )
     * @Rest\View(
     *
     * )
     */
    public function getArticlesList(ArticleRepository $repo){
        $order="desc";
        $articles=$repo->findListArticles($order);
        for ($i = 1; $i < count($articles); $i++) {
            $articles[$i]->setDescription(substr($articles[$i]->getDescription(), 0,320)."...");

        }


        return $articles;
    }

    /**
     * @Rest\Post(
     *     path="/articles",
     *     name="createArticle"
     * )
     * @Rest\View (statusCode=201)
     * @ParamConverter("article",converter="fos_rest.request_body")
     */
    public function create(Article $article){
        $em=$this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->view
        ($article, Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('getArticlesList', ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]
        );
    }
}