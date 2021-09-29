<?php
/*
 * Gnekoz Framework for PHP applications
 * Copyright (C) 2012  Luca Stauble
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace gnekoz\http;

/**
 * @author gneko
 *
 */
class File
{
  private $info = array();

  private $id;

  private $path = null;

  public function __construct($id, $uploadInfo)
  {
    $this->id = $id;
    $this->info = $uploadInfo;
  }

  public function getID()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->info['name'];
  }

  public function getType()
  {
    return $this->info['type'];
  }

  public function getSize()
  {
    return $this->info['size'];
  }

  public function getError()
  {
    return $this->info['error'];
  }

  public function hasError()
  {
    return $this->getError() != UPLOAD_ERR_OK;
  }

  public function getTmpName()
  {
    return $this->info['tmp_name'];
  }

  public function getPath()
  {
    if ($this->path ==null)
    {
      return $this->getTmpName();
    }
    return $this->path;
  }

  public function save($filePath)
  {
    $ret = move_uploaded_file($this->getPath(), $filePath);
    if ($ret)
    {
      $this->path = $filePath;
    }
    return $ret;
  }
}
