<?php

/**
 * Created by PhpStorm.
 * User: marcinurbaniak
 * Date: 12.07.2017
 * Time: 22:50
 */
class Photo
{
    private $id;
    private $productId;
    private $pictureName;
    private $path;
    private $pictureDescription;

    public function __construct($id = -1, $productId = -1, $pictureName ='',$path ='', $pictureDescription = '')
    {


    }




    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    

    /**
     * @return mixed
     */
    public function getPictureName()
    {
        return $this->pictureName;
    }

    /**
     * @param mixed $pictureName
     */
    public function setPictureName($pictureName)
    {
        $this->pictureName = $pictureName;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPictureDescription()
    {
        return $this->pictureDescription;
    }

    /**
     * @param mixed $pictureDescription
     */
    public function setPictureDescription($pictureDescription)
    {
        $this->pictureDescription = $pictureDescription;
    }





}