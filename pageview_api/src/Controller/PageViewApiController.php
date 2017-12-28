<?php

namespace Drupal\pageview_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

define('PAGE_CONTENT_TYPE', 'page');

/**
 * Class PageViewApiController.
 */
class PageViewApiController extends ControllerBase {
  /**
   * Get the page resource as JSON.
   */
  public function viewPageResource() {
    $request = \Drupal::request();
    $apikey = $request->get('apikey');
    $nid = $request->get('nid');
    // Getting the system site API key.
    $siteapikey = \Drupal::configFactory()->getEditable('system.site')->get('siteapikey');
    $serializer = \Drupal::service('serializer');
    $node = Node::load($nid);
    if(!empty($node)) {
      $type = $node->getType();
      // Return the node in json format if the API key matches and node is of type Page.
      if ($apikey == $siteapikey && $type == PAGE_CONTENT_TYPE) {
        $output = $serializer->normalize($node, 'json');
        $response = new JsonResponse($output);
        return $response;
      }
      else {
        // Throw access denied exception.
        throw new AccessDeniedHttpException();
      }
    }
    else {
      // Throw resource not found exception.
      throw new NotFoundHttpException();
    }
  }
}
