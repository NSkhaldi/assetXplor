<?php
        
namespace SeeItAll\assetXploreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;



/**
 * Level0
 *
 * @ORM\Table(name="Level0")
 * @ORM\Entity(repositoryClass="SeeItAll\assetXploreBundle\Repository\Level0Repository")
 * @ORM\HasLifecycleCallbacks
 */




 
class Level0
{



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
     * @var string
     *
     * @ORM\Column(name="level0_name", type="string", length=255,  nullable=true)
     */
    private $level0Name;

 

    /**
     * @var string
     *
     * @ORM\Column(name="level0_image", type="string", length=255, nullable=true)
     */
    private $level0Image;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Get asset_id
     *
     * @return int
     */
    public function getIdAsset()
    {
        return $this->id_asset;
    }

    /**
     * Set id_asset
     *
     * @param string $idAsset
     *
     * @return level0
     */
    public function setIdAsset($idAsset)
    {
        $this->id_asset =$idAsset;

        return $this;
    }

    /**
     * Set level0Name
     *
     * @param string $level0Name
     *
     * @return level0
     */
    public function setlevel0Name($level0Name)
    {
        $this->level0Name =$level0Name;

        return $this;
    }
    /**
     * Get data_loc
     *
     * @return string
     */
    public function getDataLoc()
    {
        return $this->data_loc;
    }

     /**
     * Set data_loc
     *
     * @param string $Dataloc
     *
     * @return level0
     */
    public function setDataLoc($DataLoc)
    {
        $this->data_loc =$DataLoc;
        return $this;
    }

    /**
     * Get level0Name
     *
     * @return string
     */
    public function getlevel0Name()
    {
        return $this->level0Name;
    }

    /**
     * Set level0Pdf
     *
     * @param string $level0Pdf
     *
     * @return level0
     */
    public function setlevel0Pdf($level0Pdf)
    {
        $this->level0Pdf = $level0Pdf;

        return $this;
    }

    /**
     * Get level0Pdf
     *
     * @return string
     */
    public function getlevel0Pdf()
    {
        return $this->level0Pdf;
    }

    /**
     * Set level0Image
     *
     * @param string $level0Image
     *
     * @return level0
     */
    public function setlevel0Image($level0Image)
    {
        $this->level0Image = $level0Image;

        return $this;
    }

    /**
     * Get level0Image
     *
     * @return string
     */
    public function getlevel0Image()
    {
        return $this->level0Image;
    }


      /**
     * SetNote
     *
     * @param string $Note
     *
     * @return level0
     */
    public function setNote($Note)
    {
        $this->note =$Note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }


        /**
     * SetContract_number
     *
     * @param string $Contract_number
     *
     * @return level0
     */
    public function setContractNumber($Contract_number)
    {
        $this->contract_number =$Contract_number;

        return $this->contract_number;
    }

    /**
     * Get Contract_number
     *
     * @return string
     */
    public function getContractNumber()
    {
        return $this->contract_number;
    }






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
    if (null !== $this->level0Name) {
      // On sauvegarde l'extension du fichier pour le supprimer plus tard
      $this->tempFilename = $this->level0Name;


      // On réinitialise les valeurs des attributs url et alt
      $this->level0Image = null;
      $this->level0Name = null;
    }
  }


private $uniqid;


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
            $this->uniqid = uniqid();
              //$this->level0Name = date('Y-m-d H:i:s');
            $this->level0Name = $this->file->getClientOriginalName();
             $this->level0Image = $this->getUploadDir().'/'.$this->uniqid;
             
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
      //$storage = new StorageClient();  

      //move_uploaded_file($this->file, "gs://assetxplor/".$this->level0Name);
 // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
         $this->getUploadRootDir(), // Le répertoire de destination
             $this->uniqid // Le nom du fichier à créer, ici « id.extension »
            );
      
     // move_uploaded_file($this->file, "gs://${my_bucket}/{$this->level0Name}");
 
    }


   /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    $this->tempFilename = $this->getUploadRootDir().'/'.$this->level0Name;
  }

  /**
   * @ORM\PostRemove()
   */
  public function removeUpload()
  {
    // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
    if (file_exists($this->tempFilename)) {
      // On supprime le fichier
      unlink($this->tempFilename);
    }
  }

  
    public function getUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'uploads';
    }
  
    protected function getUploadRootDir()
    {
      // On retourne le chemin relatif vers l'image pour notre code PHP
      return __DIR__.'/../../../../web/'.$this->getUploadDir();
       
    }
    

}