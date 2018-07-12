<?php
        
namespace SeeItAll\assetXploreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

//use Google\Cloud\Storage\StorageClient;


# Your Google Cloud Platform project ID
//$projectId = 'assetxplor';

# Instantiates a client
//$storage = new StorageClient([
   // 'projectId' => $projectId
//]);

/**
 * Level4
 *
 * @ORM\Table(name="Level4")
 * @ORM\Entity(repositoryClass="SeeItAll\assetXploreBundle\Repository\Level4Repository")
 * @ORM\HasLifecycleCallbacks
 */
class Level4
{
    private $uniqid ;

  /**
   * @ORM\ManyToOne(targetEntity="SeeItAll\assetXploreBundle\Entity\Level3" )
   * @ORM\JoinColumn(nullable=true, onDelete="SET NULL" )  
   */
//@ORM\JoinColumn(nullable=true) prohibits the creation of rooms without a level4
    private $level3;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_asset", type="integer", nullable=true)
     */
    private $id_asset;

      /**
     * @var int
     *
     * @ORM\Column(name="contract_number", type="integer", nullable=true )
     */
    private $contract_number;

     /**
    *  @var string
    * @ORM\Column(name="360_url", type="string", length=400, nullable=true)
    */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="level4_name", type="string", length=255,  nullable=true)
     */
    private $level4Name;

 

    /**
     * @var string
     *
     * @ORM\Column(name="img_path", type="string", length=255, nullable=true)
     */
    private $img_path;

        /**
     * @var string
     *
     * @ORM\Column(name="thumb_path", type="string", length=255, nullable=true)
     */
    private $thumb_path;

    /**
    *  @var string
    * @ORM\Column(name="note", type="string", length=50, nullable=true)
    */
    private $note;


        /**
    *  @var string
    * @ORM\Column(name="data_loc", type="string", length=400, nullable=true)
    */
    private $data_loc;


    /**
    *  @var string
    * @ORM\Column(name="map_path", type="string", length=400, nullable=true)
    */
    private $map_path;
    





 private $file;

// On ajoute cet attribut pour y stocker le nom du fichier temporairement
  private $tempFilename;


    public function getFile()
  {
    return $this->file;
  }

  // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
  public function setFile(UploadedFile $file)
  {
    $this->file = $file;

    // On vérifie si on avait déjà un fichier pour cette entité
    if (null !== $this->level4Name) {
      // On sauvegarde l'extension du fichier pour le supprimer plus tard
      $this->tempFilename = $this->level4Name;


      // On réinitialise les valeurs des attributs url et alt
      $this->level4Image = null;
      $this->level4Name = null;
    }
  }

  public function getUniqiD(){
    return $this->uniqid;
}

public function setUniqiD()
{
    return $this->uniqid= uniqid();
}

  /**
   * @ORM\PreUpdate()
   *  @ORM\PrePersist()    
   * 
   */
  public function preUpload()
  {
    // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
    if (null === $this->file) {
      return;
    }
    $this->setUniqId();
    $this->level4Name = $this->file->getClientOriginalName();
    $this->img_path = $this->getUploadDir().$this->getUniqId();
    $this->thumb_path=  $this->getThumbUploadDir().$this->getUniqId().'_thumb';
             
  }


 


  /**
   * @ORM\PostPersist()
   * @ORM\PostUpdate()
   */

 public function upload()
    {
      // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
      if (null === $this->file) {
        return;
      }
        //Create and upoad the thumbnail
        $this->generateThumbnail($this->file, 300, 150, $quality = 75);

        //upload the full size image
        $this->file->move(
            $this->getUploadRootDir().$this->getUploadDir(), // Le répertoire de destination
            $this->uniqid // Le nom du fichier à créer, ici « id.extension »
           );
      
 
    }


   /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    $this->tempFilename = $this->getUploadRootDir().'/'.$this->level4Name;
  }

  /**
   * @ORM\PostRemove()
   */
  public function removeUpload()
  {
    // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
    if (file_exists($this->tempFilename)) {
      // On supprime le fichier
      //unlink($this->tempFilename);
    }
  }

  
  public function getUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'img/';
    }
    public function getMapsUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'maps/';
    }

    public function getThumbUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'optim/';
    }
  
    public function getUploadRootDir()
    {
      // On retourne le chemin relatif vers l'image pour notre code PHP
      return __DIR__.'/../../../../web/';
       
    }

  public function generateThumbnail($img, $width, $height, $quality = 90)
  {
      if (is_file($img)) {
          $imagick = new \Imagick(realpath($img));
          $imagick->setImageFormat('jpeg');
          $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
          $imagick->setImageCompressionQuality($quality);
          $imagick->thumbnailImage($width, $height, false, false);
          //$filename_no_ext = reset(explode('.', $img));
          if (file_put_contents( $this->getUploadRootDir().$this->getThumbUploadDir().$this->getUniqid().'_thumb', $imagick) === false) {
            throw new Exception("Could not put contents.");
        }
          return true;
      }
      else {
          throw new Exception("No valid image provided with {$img}.");
      }
  }
  
    






    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idAsset.
     *
     * @param int|null $idAsset
     *
     * @return Level4
     */
    public function setIdAsset($idAsset = null)
    {
        $this->id_asset = $idAsset;

        return $this;
    }

    /**
     * Get idAsset.
     *
     * @return int|null
     */
    public function getIdAsset()
    {
        return $this->id_asset;
    }

    /**
     * Set contractNumber.
     *
     * @param int|null $contractNumber
     *
     * @return Level4
     */
    public function setContractNumber($contractNumber = null)
    {
        $this->contract_number = $contractNumber;

        return $this;
    }

    /**
     * Get contractNumber.
     *
     * @return int|null
     */
    public function getContractNumber()
    {
        return $this->contract_number;
    }

    /**
     * Set level4Name.
     *
     * @param string|null $level4Name
     *
     * @return Level4
     */
    public function setLevel4Name($level4Name = null)
    {
        $this->level4Name = $level4Name;

        return $this;
    }

    /**
     * Get level4Name.
     *
     * @return string|null
     */
    public function getLevel4Name()
    {
        return $this->level4Name;
    }

    /**
     * Set level4Image.
     *
     * @param string|null $level4Image
     *
     * @return Level4
     */
    public function setLevel4Image($level4Image = null)
    {
        $this->level4Image = $level4Image;

        return $this;
    }

    /**
     * Get level4Image.
     *
     * @return string|null
     */
    public function getLevel4Image()
    {
        return $this->level4Image;
    }

    /**
     * Set note.
     *
     * @param string|null $note
     *
     * @return Level4
     */
    public function setNote($note = null)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note.
     *
     * @return string|null
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set dataLoc.
     *
     * @param string|null $dataLoc
     *
     * @return Level4
     */
    public function setDataLoc($dataLoc = null)
    {
        $this->data_loc = $dataLoc;

        return $this;
    }

    /**
     * Get dataLoc.
     *
     * @return string|null
     */
    public function getDataLoc()
    {
        return $this->data_loc;
    }

    /**
     * Set level3.
     *
     * @param \SeeItAll\assetXploreBundle\Entity\Level3|null $level3
     *
     * @return Level4
     */
    public function setLevel3(\SeeItAll\assetXploreBundle\Entity\Level3 $level3 = null)
    {
        $this->level3 = $level3;

        return $this;
    }

    /**
     * Get level3.
     *
     * @return \SeeItAll\assetXploreBundle\Entity\Level3|null
     */
    public function getLevel3()
    {
        return $this->level3;
    }

    /**
     * Set imgPath.
     *
     * @param string|null $imgPath
     *
     * @return Level4
     */
    public function setImgPath($imgPath = null)
    {
        $this->img_path = $imgPath;

        return $this;
    }

    /**
     * Get imgPath.
     *
     * @return string|null
     */
    public function getImgPath()
    {
        return $this->img_path;
    }

    /**
     * Set thumbPath.
     *
     * @param string|null $thumbPath
     *
     * @return Level4
     */
    public function setThumbPath($thumbPath = null)
    {
        $this->thumb_path = $thumbPath;

        return $this;
    }

    /**
     * Get thumbPath.
     *
     * @return string|null
     */
    public function getThumbPath()
    {
        return $this->thumb_path;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return Level4
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set mapPath.
     *
     * @param string|null $mapPath
     *
     * @return Level4
     */
    public function setMapPath($mapPath = null)
    {
        $this->map_path = $mapPath;

        return $this;
    }

    /**
     * Get mapPath.
     *
     * @return string|null
     */
    public function getMapPath()
    {
        return $this->map_path;
    }
}
