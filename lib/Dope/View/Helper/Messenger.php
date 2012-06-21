<?php

class Dope_View_Helper_Messenger extends Zend_View_Helper_Abstract {
	
	/**
	 * $_session - Zend_Session storage object
	 *
	 * @var Zend_Session
	 */
	protected static $_session = null;
	
	/**
	 * Add a message to the collection of priority messages or retrieve the
	 * priority messages. If $messages is left null then all the messages are
	 * returned, unless $severity is set (as a string or array), causing just
	 * the indicated messages to be returned; in either case, the returned
	 * messages are cleared from the session cache. If a message or messages
	 * are provided, then store them in the indicated severity; $messages may
	 * be an array of string all to be stored in the indicated severity OR it
	 * may be an associative array of severity-to-message pairs. In any case,
	 * if $severity is not set but message is, then 'info' is the assumed
	 * default.
	 *
	 * @param  string|array|null $message
	 * @param  string|array|null $severity
	 * @return Zend_Session_Namespace
	 * @deprecated
	 */
	function messenger($message = null, $severity = null) {
		    		
		throw new Exception("OBSOLETE");
		
		$session = $this->_getSession ();
		// if page messages has not been set o the session, then initialize it.
		if (! isset ( $session->page_messages )) {
			$this->_resetMessageArray ();
		}
		
		if (is_null ( $message )) {
			// return all the messages or just those indicated by $severity
			return $this->_resetMessageArray ( $severity );
		} else {
			//add message to the collection
			

			// default severity to 'info'
			if (is_null ( $severity )) {
				$severity = 'info';
			}
			
			// if severity is an array, then assume this was done in error and use
			// only the first value of the array as the severity.
			if (is_array ( $severity )) {
				reset ( $severity );
				$severity = $severity [key ( $severity )];
			}
			
			// if this is the first message of this severity, then initialize
			// the message array for that severity.
			if (! isset ( $session->page_messages [$severity] )) {
				$session->page_messages [$severity] = array ();
			}
			
			// if message is an array then assume it is a group of messages to be
			// added with the given severity. However, it is possible to pass in an
			// array of arrays of severity-to-messageArray sets, going as deep as
			// one might wish, so long as the deepest level provides the correct
			// severity-to-message relationship.
			if (is_array ( $message )) {
				foreach ( $message as $sev => $mes ) {
					$this->messenger ( $mes, $sev );
				}
			} else {
				//$storage[$severity][] = $message;
				//$session->getStorage()->write($storage);
				

				$session->page_messages [$severity] [] = $message;
			}
			//print_r($session);
		}
		
	}
	
	/**
	 * Reset the session object's collection of messages. If severity provided,
	 * then return that severity and clear only that severity. If an array of
	 * severities are provided, then return an array in the form of
	 * $severity=>$messages.
	 *
	 * @param  string|array $severity
	 * @return void
	 */
	private function _resetMessageArray($severity = null) {
		$messages = array ();
		$session = $this->_getSession ();
		if (is_null ( $severity )) {
			$messages = (isset($session->page_messages)) ? $session->page_messages : '';
			$session->page_messages = array ();
		} elseif (is_string ( $severity ) && isset ( $session->page_messages [$severity] )) {
			$messages = $session->page_messages [$severity];
			unset ( $session->page_messages [$severity] );
		} elseif (is_array ( $severity )) {
			foreach ( $severity as $sev ) {
				$messages [$sev] = $this->_resetMessageArray ( $sev );
			}
		}
		return $messages;
	}
	
	/**
	 * Return the static session object, initiating it if needs be.
	 * 
	 * This used to use Zend_Auth's session, but no longer. 
	 *
	 * @return Zend_Session_Namespace
	 */
	private function _getSession() {
		return new Zend_Session_Namespace('application');
	}
}
