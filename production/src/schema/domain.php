<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Domain
 *
 * @ORM\Table(name="domains")
 * @ORM\Entity
 */
class Domain
{
    /**
     * @var string
     *
     * @ORM\Column(name="domainname", type="string", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $domainName;

    /**
     * @var integer
     *
     * @ORM\Column(name="bot_type", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $bot_type;

    /**
     * @var string
     *
     * @ORM\Column(name="bot_username", type="string", precision=0, scale=0, nullable=false, unique=false)
     */
    private $bot_username;

    /**
     * @var string
     *
     * @ORM\Column(name="bot_password", type="string", precision=0, scale=0, nullable=false, unique=false)
     */
    private $bot_password;

    /**
     * @var integer
     *
     * @ORM\Column(name="port_number", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $port_number;

    function __construct($domainName, $bot_type){
        $this->domainName= $domainName;
        $this->bot_type = $bot_type;
    }

    /**
     * Set domainName
     *
     * @param string $domainName
     * @return Customer
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;

        return $this;
    }

    /**
     * Get domainName
     *
     * @return string 
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * Set bot_type
     *
     * @param integer $botType
     * @return Customer
     */
    public function setBotType($botType)
    {
        $this->bot_type = $botType;

        return $this;
    }

    /**
     * Get bot_type
     *
     * @return integer 
     */
    public function getBotType()
    {
        return $this->bot_type;
    }

    /**
     * Set bot_username
     *
     * @param string $botUsername
     * @return Customer
     */
    public function setBotUsername($botUsername)
    {
        $this->bot_username = $botUsername;

        return $this;
    }

    /**
     * Get bot_username
     *
     * @return string 
     */
    public function getBotUsername()
    {
        return $this->bot_username;
    }

    /**
     * Set bot_password
     *
     * @param string $botPassword
     * @return Customer
     */
    public function setBotPassword($botPassword)
    {
        $this->bot_password = $botPassword;

        return $this;
    }

    /**
     * Get bot_password
     *
     * @return string 
     */
    public function getBotPassword()
    {
        return $this->bot_password;
    }

    /**
     * Set port_number
     *
     * @param integer $portNumber
     * @return Customer
     */
    public function setPortNumber($portNumber)
    {
        $this->port_number = $portNumber;

        return $this;
    }

    /**
     * Get port_number
     *
     * @return integer 
     */
    public function getPortNumber()
    {
        return $this->port_number;
    }
}
