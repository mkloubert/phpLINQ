<?php

/**
 *  LINQ concept for PHP
 *  Copyright (C) 2015  Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 *
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 3.0 of the License, or (at your option) any later version.
 *
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *    Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public
 *    License along with this library.
 */

namespace System\IO;

use \System\ClrString;
use \System\ILazy;
use \System\IString;
use \System\Lazy;
use \System\Collections\IEnumerable;
use \System\Linq\Enumerable;


/**
 * An object that provides information about a directory.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DirectoryInfo extends FileSystemInfo implements IDirectoryInfo {
    /**
     * @var ILazy
     */
    private $_directory;
    /**
     * @var bool
     */
    private $_exists;
    /**
     * @var IString
     */
    private $_fullName;
    /**
     * @var IString
     */
    private $_name;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $path The path of the directory.
     */
    public function __construct($path) {
        $path = new ClrString(static::normalizePath($path));
        if ($path->endsWith(':')) {
            $path = $path->append(\DIRECTORY_SEPARATOR);
        }

        $this->_fullName = $path;
        $this->_name     = new ClrString(\basename($path));

        $this->refresh();
    }


    /**
     * {@inheritDoc}
     */
    public final function directory() {
        return $this->_directory
                    ->value();
    }

    /**
     * {@inheritDoc}
     */
    public final function directoryName() {
        return null !== $this->directory() ? $this->directory()->fullName()
                                           : null;
    }

    /**
     * {@inheritDoc}
     */
    public final function enumerateDirectories() : IEnumerable {
        $path = (string)$this->_fullName;

        $me = $this;

        return Enumerable::create(\scandir($path))
                         ->select(function($x) use ($path) {
                                      if (ClrString::isNullOrWhitespace($x)) {
                                          return null;
                                      }

                                      if ('.' === \trim($x)) {
                                          return null;
                                      }

                                      if ('..' === \trim($x)) {
                                          return null;
                                      }

                                      $fullPath = \realpath($path . \DIRECTORY_SEPARATOR . $x);
                                      if (false === $fullPath) {
                                          return null;
                                      }

                                      $scopePath = $fullPath;

                                      if (@\is_link($scopePath)) {
                                          $link = @\readlink($scopePath);
                                          if (false !== $link) {
                                              $scopePath = $link;
                                          }
                                      }

                                      if (!\is_dir($scopePath)) {
                                          return null;
                                      }

                                      return $fullPath;
                                  })
                         ->ofType('string')
                         ->select(function($x) use ($me) : IDirectoryInfo {
                                      return $me->getType()
                                                ->newInstance($x);
                                  });
    }

    /**
     * {@inheritDoc}
     */
    public final function enumerateFiles() : IEnumerable {
        $path = (string)$this->_fullName;

        return Enumerable::create(\scandir($path))
                         ->select(function($x) use ($path) {
                                      if (ClrString::isNullOrWhitespace($x)) {
                                          return null;
                                      }

                                      if ('.' === \trim($x)) {
                                          return null;
                                      }

                                      if ('..' === \trim($x)) {
                                          return null;
                                      }

                                      $fullPath = \realpath($path . \DIRECTORY_SEPARATOR . $x);
                                      if (false === $fullPath) {
                                          return null;
                                      }

                                      $scopePath = $fullPath;

                                      if (@\is_link($scopePath)) {
                                          $link = @\readlink($scopePath);
                                          if (false !== $link) {
                                              $scopePath = $link;
                                          }
                                      }

                                      if (!\is_file($scopePath)) {
                                          return null;
                                      }

                                      return $fullPath;
                                  })
                         ->ofType('string')
                         ->select('$x => new \System\IO\FileInfo($x)');
    }

    /**
     * {@inheritDoc}
     */
    public final function exists() : bool {
        return $this->_exists;
    }

    /**
     * {@inheritDoc}
     */
    public final function fullName() {
        return $this->_fullName;
    }

    /**
     * {@inheritDoc}
     */
    public final function getDirectories($predicate = null) : array {
        $seq = $this->enumerateDirectories();

        if (null !== $predicate) {
            $seq = $seq->where($predicate);
        }

        return $seq->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public final function getFiles($predicate = null) : array {
        $seq = $this->enumerateFiles();

        if (null !== $predicate) {
            $seq = $seq->where($predicate);
        }

        return $seq->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public final function name() {
        return $this->_name;
    }

    /**
     * {@inheritDoc}
     */
    public function refresh() {
        // reset first
        $this->_directory = null;
        $this->_exists    = false;

        $me = $this;

        $path = (string)$this->_fullName;
        if (@\is_link($path)) {
            $link = @\readlink($path);
            if (false !== $link) {
                $path = $link;
            }
        }

        // does exist?
        $this->_exists = \file_exists($path) &&
                         \is_dir($path);

        // parent directory
        $this->_directory = new Lazy(function() use ($me) {
            $result = null;

            $np = $me->getType()
                     ->getMethod('normalizePath')->getClosure(null);

            $dirPath = $me->fullName()->asImmutable();
            if (!$dirPath->endsWith(\DIRECTORY_SEPARATOR)) {
                $dirPath = $dirPath->append(\DIRECTORY_SEPARATOR);
            }
            $dirPath = $dirPath->append('..');

            $dirPath = $np($dirPath);

            if (!ClrString::isNullOrWhitespace($dirPath)) {
                $result = $me->getType()
                             ->newInstance($dirPath);
            }

            return $result;
        });
    }

    /**
     * Throws an exception if the directory does not exist.
     *
     * @throws DirectoryNotFoundException The directory does not exist.
     */
    protected final function throwIfNotExist() {
        if (!$this->exists()) {
            throw new DirectoryNotFoundException($this->fullName());
        }
    }
}
