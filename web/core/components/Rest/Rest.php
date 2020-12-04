<?php

namespace Nick\Rest;

use Nick\Entity\EntityInterface;
use Nick\Rest\Entity\Client;
use Nick\Rest\Entity\ClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Rest
 *
 * @package Nick\Rest
 */
class Rest {

  /**
   * Returns a message to the client
   *
   * @param     $message
   * @param int $status
   */
  public static function message($message, int $status = 400) {
    static::data([], $message, $status);
  }

  /**
   * Returns data to the client
   *
   * @param array       $data
   * @param int         $status
   * @param string|null $message
   */
  public static function data(array $data = [], int $status = 200, ?string $message = NULL) {
    $return_array = ['status' => $status ?? 200];
    if (count($data) > 0) {
      $return_array['data'] = $data;
    }
    if (!empty($message)) {
      $return_array['message'] = $message;
    }

    $response = new JsonResponse();
    $response->setContent(json_encode($return_array));
    $response->setStatusCode($status);
    $response->send();
    exit;
  }

  /**
   * @param array $information
   *
   * @return bool
   */
  public static function auth(array $information): bool {
    if (!$information['uuid']) {
      static::message('No authorization method was given.', 401);
    }

    /** @var Client $client */
    $client = \Nick::EntityManager()
      ->loadByProperties([
        'type' => 'client',
        'uuid' => $information['uuid'],
      ]);
    if (!$client instanceof ClientInterface) {
      static::message('Client does not exist.', 401);
    }
    if (!$client->hasPermission('retrieve')) {
      static::message('Client has no access to this function.', 405);
    }

    return TRUE;
  }

  /**
   * Retrieves information from Nick to the front-end
   *
   * @param array $information
   */
  public static function Retrieve(array $information) {
    static::auth($information);

    if (!isset($information['entity'])) {
      static::message('No entity was given.', 400);
    }

    /** @var EntityInterface $data */
    $data = \Nick::EntityManager()
      ->loadByProperties($information['entity']);
    if (!$data instanceof EntityInterface) {
      static::message('Requested entity does not exist or is corrupt.', 404);
    }

    static::data($data->getValues(), 200, 'Data was successfully retrieved.');
  }

  /**
   * Transmits information from the front-end to the Nick
   *
   * @param array $information
   */
  public static function Transmit(array $information) {
    static::auth($information);

    if (!isset($information['entity'])) {
      static::message('No entity was given.', 400);
    }

    if (!isset($information['new-values'])) {
      static::message('No new values were given, can\'t save entity. [usage: add data key "new-values" with array of values]', 412);
    }

    /** @var EntityInterface $data */
    $data = \Nick::EntityManager()
      ->loadByProperties($information['entity']);
    if (!$data instanceof EntityInterface) {
      static::message('Requested entity does not exist or is corrupt.', 404);
    }

    foreach ($information['new-values'] as $key => $value) {
      $data->setValue($key, $value);
    }
    if (!$data->save()) {
      static::message('Something went wrong trying to save the entity', 400);
    }

    static::data($data->getValues(), 200, 'Data was saved to the database');
  }



}