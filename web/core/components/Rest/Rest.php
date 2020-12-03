<?php

namespace Nick\Rest;

use Nick\Entity\EntityInterface;
use Nick\Rest\Entity\Client;
use Nick\Rest\Entity\ClientInterface;

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
   *
   * @return array|int[]
   */
  public static function message($message, int $status = 400) {
    return static::data([], $message, $status);
  }

  /**
   * Returns data to the client
   *
   * @param array       $data
   * @param int         $status
   * @param string|null $message
   *
   * @return array
   */
  public static function data(array $data = [], int $status = 200, ?string $message = NULL) {
    $return_array = ['status' => $status ?? 200];
    if (count($data) > 0) {
      $return_array['data'] = $data;
    }
    if (!empty($message)) {
      $return_array['message'] = $message;
    }
    return $return_array;
  }

  /**
   * Retrieves information from Nick to the front-end
   *
   * @param $information
   *
   * @return array
   */
  public static function Retrieve($information): array {
    if (!$information['uuid']) {
      return ['message' => 'No authorization method was given.'];
    }

    /** @var Client $client */
    $client = \Nick::EntityManager()
      ->loadByProperties([
        'type' => 'client',
        'uuid' => $information['uuid'],
      ]);
    if (!$client instanceof ClientInterface) {
      return static::message('Client does not exist.', 401);
    }
    if (!$client->hasPermission('retrieve')) {
      return static::message('Client has no access to this function.', 405);
    }

    if (!isset($information['entity'])) {
      return static::message('No entity was given.', 400);
    }

    /** @var EntityInterface $data */
    $data = \Nick::EntityManager()
      ->loadByProperties($information['entity']);
    if (!$data instanceof EntityInterface) {
      return static::message('Requested entity does not exist or is corrupt.', 404);
    }

    return static::data($data->getValues(), 200, 'Data was successfully retrieved.');
  }

  /**
   * Transmits information from the front-end to the Nick
   *
   * @param $information
   *
   * @return array
   */
  public static function Transmit($information): array {
    if (!$information['uuid']) {
      return ['message' => 'No authorization method was given.'];
    }

    /** @var Client $client */
    $client = \Nick::EntityManager()
      ->loadByProperties([
        'type' => 'client',
        'uuid' => $information['uuid'],
      ]);
    if (!$client instanceof ClientInterface) {
      return ['message' => 'Client does not exist.'];
    }
    if (!$client->hasPermission('transmit')) {
      return ['message' => 'Client has no access to this function.'];
    }

    if (!isset($information['entity'])) {
      return static::message('No entity was given.', 400);
    }

    if (!isset($information['new-values'])) {
      return static::message('No new values were given, can\'t save entity. [usage: add data key "new-values" with array of values]', 412);
    }

    /** @var EntityInterface $data */
    $data = \Nick::EntityManager()
      ->loadByProperties($information['entity']);
    if (!$data instanceof EntityInterface) {
      return static::message('Requested entity does not exist or is corrupt.', 404);
    }

    foreach ($information['new-values'] as $key => $value) {
      $data->setValue($key, $value);
    }
    if (!$data->save()) {
      return static::message('Something went wrong trying to save the entity', 400);
    }

    return static::data($data->getValues(), 200, 'Data was saved to the database');
  }



}