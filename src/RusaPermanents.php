<?php

/**
 * @file
 *  RusaPermanents.php
 *
 * @Creted 
 *  2018-02023 - Paul Lieberman
 *
 * Gets and holds Permanent data
 */

namespace Drupal\rusa_api;

use Drupal\rusa_api\Client\RusaClient;

/**
 * Gets and holds perm data
 *
 */
class RusaPermanents {

  protected $perms;

  public function __construct($params = []) {
    $query = ['dbname' => 'permanents'];
    if (!empty($params['key']) && !empty($params['val'])) {
      $query += $params;
    }

    // Get perms from gdbm
    $client    = new RusaClient();
    $db_perms =  $client->get($query);

    foreach ($db_perms as $perm) {
      $this->perms[$perm->pid] = $perm;
    }  

  }

  /**
   * Get all Permanents
   *
   */
  public function getPermanents(){
    return $this->perms;
  }

  /**
   * Get active Permanents
   *
   */
  public function getActivePermanents(){
    $perms = [];
    foreach ($this->perms as $pid => $perm) {
      if ($perm->active == '1') {
        $perms[$pid] = $perm;
      }
    }
    return $perms;

  }

  /**
   * Get single Permanent by id
   *
   */
  public function getPermanent($pid){
    return $this->perms[$pid];
  }

  /** Get Permanents by distance 
   *  Only perms between distance and +20% of distance
   */
  public function getPermanentsByDistance($dist){
    $results = [];
    foreach ($this->perms as $pid => $perm) {
      if ($perm->dist >= $dist && $perm->dist <= $dist * 1.2 && $perm->active == '1') {
        $results[$pid] = $perm;
      }
    }
    return $results;
  }

  /**
   * Get single Permanent distance
   *
   */
  public function getPermanentDistance($pid){
    return $this->perms[$pid]->dist;
  }

  /**
   * Get perm line
   *
   * Combines distance, perm name, and start into a string
   *
   */
  public function getPermanentLine($pid) {
    $perm = $this->perms[$pid];

    $perm_name = empty($perm->name) ? "Unnamed perm" : $perm->name;
    $perm_line = "Permanent: " .  $pid .  " (" . $perm->dist . "km) " .  "\"" . $perm_name . "\"  " .  $perm->start;
   
    return $perm_line;
  }

  /**
   * Get perms sorted by distance and id
   *
   */
  public function getPermanentsSorted() {
    $perms = $this->perms;

    // Sort by distance and then perm id
    foreach ($perms as $key => $perm) {
      $asort[$key] = $perm->dist;
      $bsort[$key] = $perm->pid;;
    }

    array_multisort($asort, SORT_NUMERIC, SORT_ASC,
                    $bsort, SORT_NUMERIC, SORT_ASC,
                    $perms);
    return $perms;
  }

  /**
   * Get perms by owner
   *
   */
  public function getPermsByOwner($mid) {
    $perms = [];
    foreach ($this->perms as $pid => $perm) {
      if ($perm->mid == $mid && $perm->status == 1) {
        $perms[$pid] = $perm;
      }
    }
    return $perms;
  }


} // End of class