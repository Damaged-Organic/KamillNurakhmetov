<?php
// src/AppBundle/Controller/ReaderController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Controller\Contract\PageCleanupInterface,
    AppBundle\Security\Authorization\Voter\StoryVoter,
    AppBundle\Security\Authorization\Voter\BookVoter;

class ReaderController extends Controller implements PageCleanupInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/reader/book/{bookId}/{chapterId}",
     *      name="reader_book",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%", "chapterId" = null},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "bookId" = "\d+", "chapterId" = "\d+"}
     * )
     * @Route(
     *      "/reader/book/{bookId}/{chapterId}",
     *      name="reader_book_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%", "chapterId" = null},
     *      requirements={"domain" = "%domain%", "bookId" = "\d+", "chapterId" = "\d+"}
     * )
     */
    public function readerBookAction(Request $request, $bookId, $chapterId = NULL)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_authorizationChecker = $this->get('security.authorization_checker');

        $_viewsCounter = $this->get('app.views_counter');

        $_bookmark = $this->get('app.bookmark');

        /*if( !$_authorizationChecker->isGranted('ROLE_READER') )
            throw $this->createAccessDeniedException();*/

        $book = $_manager->getRepository('AppBundle:Book')->find($bookId);

        if( !$book )
            throw $this->createNotFoundException();

        if( !$_authorizationChecker->isGranted('BOOK_ACQUIRED', $book) ) {
            $chapter = $_manager->getRepository('AppBundle:Chapter')->findFirstChapterByBook($book);
        } else {
            $bookmark = $_bookmark->getBookmark($this->getUser(), $book);

            if( $chapterId ) {
                $chapter = $_manager->getRepository('AppBundle:Chapter')->findAnyChapterByBook($book, $chapterId);
            } elseif( $bookmark ) {
                $chapter = $bookmark->getChapter();
            } else {
                $chapterId = $_manager->getRepository('AppBundle:Chapter')->findFirstChapterByBook($book);
                $chapter   = $_manager->getRepository('AppBundle:Chapter')->findAnyChapterByBook($book, $chapterId);
            }
        }

        if( !$chapter )
            throw $this->createNotFoundException();

        $sequence = [
            'previous' => $_manager->getRepository('AppBundle:Chapter')->findPreviousChapter($book, $chapter->getChapterOrder()),
            'next'     => $_manager->getRepository('AppBundle:Chapter')->findNextChapter($book, $chapter->getChapterOrder())
        ];

        if( !$_viewsCounter->isAlreadyCounted(get_class($book), $book->getId()) )
            $book->setViews($book->getViews() + 1);

        $_manager->persist($book);
        $_manager->flush();

        $_bookmark->setBookmark($this->getUser(), $book, $chapter);

        return $this->render('AppBundle:Reader:reader.html.twig', [
            'book'     => $book,
            'sequence' => $sequence,
            'content'  => $chapter
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/reader/book/chapter",
     *      name="reader_book_chapter",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     * @Route(
     *      "/reader/book/chapter",
     *      name="reader_book_chapter_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%"}
     * )
     */
    public function readerBookChapter(Request $request)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_authorizationChecker = $this->get('security.authorization_checker');

        $_bookmark = $this->get('app.bookmark');

        if( !$_authorizationChecker->isGranted('ROLE_READER') )
            throw $this->createAccessDeniedException();

        if( !$request->query->has('bookId') || !$request->query->get('contentsId') )
            throw $this->createNotFoundException();

        $book = $_manager->getRepository('AppBundle:Book')->find(
            $request->query->get('bookId')
        );

        if( !$book )
            throw $this->createNotFoundException();

        if( !$_authorizationChecker->isGranted('BOOK_ACQUIRED', $book) )
            throw $this->createAccessDeniedException();

        $chapter = $_manager->getRepository('AppBundle:Chapter')->findAnyChapterByBook(
            $book,
            $request->query->get('contentsId')
        );

        if( !$chapter )
            throw $this->createNotFoundException();

        $_bookmark->setBookmark($this->getUser(), $book, $chapter);

        $sequence = [
            'previous' => $_manager->getRepository('AppBundle:Chapter')->findPreviousChapter($book, $chapter->getChapterOrder()),
            'next'     => $_manager->getRepository('AppBundle:Chapter')->findNextChapter($book, $chapter->getChapterOrder())
        ];

        $content = [
            'chapter' => $this->renderView('AppBundle:Reader:chapter.html.twig', [
                'chapter' => $chapter
            ]),
            'prev_link' => ( $sequence['previous'] ) ? $sequence['previous']->getId() : '#',
            'next_link' => ( $sequence['next'] ) ? $sequence['next']->getId() : '#'
        ];

        return new Response(json_encode($content));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/reader/story/{storyId}",
     *      name="reader_story",
     *      host="{_locale}.{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%", "bookId" = "\d+"}
     * )
     * @Route(
     *      "/reader/story/{storyId}",
     *      name="reader_story_default",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"domain" = "%domain%", "bookId" = "\d+"}
     * )
     */
    public function readerStoryAction($storyId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_authorizationChecker = $this->get('security.authorization_checker');

        $_viewsCounter = $this->get('app.views_counter');

        $story = $_manager->getRepository('AppBundle:Story')->find($storyId);

        if( !$_authorizationChecker->isGranted(StoryVoter::STORY_READER_SUBSCRIBED, $story) )
            throw $this->createAccessDeniedException();

        if( !$story )
            throw $this->createNotFoundException();

        if( !$_viewsCounter->isAlreadyCounted(get_class($story), $story->getId()) )
            $story->setViews($story->getViews() + 1);

        $_manager->persist($story);
        $_manager->flush();

        if( !$_authorizationChecker->isGranted('STORY_READER_SUBSCRIBED', $story) )
            throw $this->createAccessDeniedException();

        // KLUDGE: set sorting and filter parameters
        $_session = $this->get('session');

        $parameters = [
            'sortingParameter' => $_session->get('sorting_parameter'),
            'filterParameter'  => $_session->get('filter_parameter'),
            'page'             => $_session->get('page')
        ];

        return $this->render('AppBundle:Reader:reader.html.twig', [
            'content'    => $story,
            'parameters' => $parameters
        ]);
    }
}
