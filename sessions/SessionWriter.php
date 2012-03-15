<?php
abstract class SessionWriter {
  /**
   * Reads a session into the session object.
   * Virtual Function, implementation would need to be defined in the concrete super class.
   */
  public function read($hash) {
  }
  
  /**
   * Writes a session object.
   * Virtual Function, implementation would need to be defined in the concrete super class.
   */
  public function write($hash, $sessionObject) {
  }
  
  /**
   * Clears a session.
   * @param $hash The hash/key of the sessions.
   * Virtual Function, implementation would need to be defined in the concrete super class.
   */
  public function clear($hash) {
  }
}