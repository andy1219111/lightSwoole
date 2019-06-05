<?php
/**
 * 文件缓存
 *
 * @subpackage	Libraries
 * @category	lib
 * @author		aaron
 */
class Cache_file {

	/**
	 * Directory in which to save cache files
	 *
	 * @var string
	 */
	protected $_cache_path;

	/**
	 * Initialize file-based cache
	 *
	 * @return	void
	 */
	public function __construct($path = '')
	{
		$this->_cache_path = ($path === '') ? ROOT_PATH.'cache/' : $path;
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch from cache
	 *
	 * @param	string	$id	Cache ID
	 * @return	mixed	Data on success, FALSE on failure
	 */
	public function get($id)
	{
		$data = $this->_get($id);
		return is_array($data) ? $data['data'] : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Save into cache
	 *
	 * @param	string	$id	Cache ID
	 * @param	mixed	$data	Data to store
	 * @param	int	$ttl	Time to live in seconds
	 * @param	bool	$raw	Whether to store the raw value (unused)
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		$contents = array(
			'time'		=> time(),
			'ttl'		=> $ttl,
			'data'		=> $data
		);

		if (write_file($this->_cache_path.$id, serialize($contents)))
		{
			chmod($this->_cache_path.$id, 0640);
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	mixed	unique identifier of item in cache
	 * @return	bool	true on success/false on failure
	 */
	public function delete($id)
	{
		return is_file($this->_cache_path.$id) ? unlink($this->_cache_path.$id) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 *
	 * @return	bool	false on failure/true on success
	 */
	public function clean()
	{
		return delete_files($this->_cache_path, FALSE, TRUE);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * Not supported by file-based caching
	 *
	 * @param	string	user/filehits
	 * @return	mixed	FALSE
	 */
	public function cache_info($type = NULL)
	{
		return get_dir_file_info($this->_cache_path);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param	mixed	key to get cache metadata on
	 * @return	mixed	FALSE on failure, array on success.
	 */
	public function get_metadata($id)
	{
		if ( ! is_file($this->_cache_path.$id))
		{
			return FALSE;
		}

		$data = unserialize(file_get_contents($this->_cache_path.$id));

		if (is_array($data))
		{
			$mtime = filemtime($this->_cache_path.$id);

			if ( ! isset($data['ttl'], $data['time']))
			{
				return FALSE;
			}

			return array(
				'expire' => $data['time'] + $data['ttl'],
				'mtime'	 => $mtime
			);
		}

		return FALSE;
	}


	// ------------------------------------------------------------------------

	/**
	 * Get all data
	 *
	 * Internal method to get all the relevant data about a cache item
	 *
	 * @param	string	$id	Cache ID
	 * @return	mixed	Data array on success or FALSE on failure
	 */
	protected function _get($id)
	{
		if ( ! is_file($this->_cache_path.$id))
		{
			return FALSE;
		}

		$data = unserialize(file_get_contents($this->_cache_path.$id));

		if ($data['ttl'] > 0 && time() > $data['time'] + $data['ttl'])
		{
			unlink($this->_cache_path.$id);
			return FALSE;
		}

		return $data;
	}

}
