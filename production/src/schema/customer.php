<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="MASTER_ACCOUNTS_CONFIG")
 * @ORM\Entity
 */
class Customer
{
    /**
     * @var string
     *
     * @ORM\Column(name="accountname", type="string", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $accountname;

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

    function __construct($accountname, $bot_type){
        $this->accountname= $accountname;
        $this->bot_type = $bot_type;
    }

    /**
     * Set accountname
     *
     * @param string $accountname
     * @return Customer
     */
    public function setAccountName($accountname)
    {
        $this->accountname = $accountname;

        return $this;
    }

    /**
     * Get accountname
     *
     * @return string 
     */
    public function getAccountName()
    {
        return $this->accountname;
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
