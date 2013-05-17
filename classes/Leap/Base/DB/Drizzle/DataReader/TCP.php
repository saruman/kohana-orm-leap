<?php

/**
 * Copyright © 2011–2013 Spadefoot Team.
 *
 * Unless otherwise noted, LEAP is licensed under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License
 * at:
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * This class is used to read data from a Drizzle database using the TCP
 * driver.
 *
 * @package Leap
 * @category Drizzle
 * @version 2013-01-22
 *
 * @abstract
 */
abstract class Base\DB\Drizzle\DataReader\TCP extends DB\SQL\DataReader\Standard {

	/**
	 * This function initializes the class.
	 *
	 * @access public
	 * @override
	 * @param DB\Connection\Driver $connection  the connection to be used
	 * @param string $sql                       the SQL statement to be queried
	 * @param integer $mode                     the execution mode to be used
	 */
	public function __construct(DB\Connection\Driver $connection, $sql, $mode = NULL) {
		$resource = $connection->get_resource();
		$command = @drizzle_query($resource, $sql);
		if (($command === FALSE) OR ! @drizzle_result_buffer($command)) {
			throw new Throwable\SQL\Exception('Message: Failed to query SQL statement. Reason: :reason', array(':reason' => @drizzle_con_error($resource)));
		}
		$this->command = $command;
		$this->record = FALSE;
	}

	/**
	 * This function frees the command reference.
	 *
	 * @access public
	 * @override
	 */
	public function free() {
		if ($this->command !== NULL) {
			@drizzle_result_free($this->command);
			$this->command = NULL;
			$this->record = FALSE;
		}
	}

	/**
	 * This function advances the reader to the next record.
	 *
	 * @access public
	 * @override
	 * @return boolean                          whether another record was fetched
	 */
	public function read() {
		$this->record = @drizzle_row_next($this->command);
		return ($this->record !== FALSE);
	}

}