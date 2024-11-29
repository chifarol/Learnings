<?php
//  lets you split a large class or a set of closely related classes into two separate interfaces, hierarchies—abstraction or implementation
//  involves two things Abstraction and Implementation 
//  The Abstraction provides high-level control logic. It relies on the implementation object to do the actual low-level work.
//  The Implementation declares the interface that’s common for all concrete implementations.

//  EXAMPLE
//  we can have a common remote controller that implement power on/off volume up/down etc (higher abstraction), these then communicates with the target device (TV, radio, cable) operating system *APIs* (implementation)

interface RemoteControlInterface
{
    public function togglePower(): void;
    public function volumeUp(): void;
    public function volumeDown(): void;
    public function channelDown(): void;
    public function channelUp(): void;
}
interface AdvancedRemoteControlInterface extends RemoteControlInterface
{
    public function mute(): void;
}
interface DeviceInterface
{
    public function isEnabled(): bool;
    public function enable(): void;
    public function disable(): void;
    public function getVolume(): int;
    public function setVolume(int $level): void;
    public function getChannel(): int;
    public function setChannel(int $channel_no): void;
}

class RemoteController implements RemoteControlInterface
{
    public function __construct(private DeviceInterface $radioDevice)
    {

    }
    public function togglePower(): void
    {
        if ($this->radioDevice->isEnabled()) {
            $this->radioDevice->disable();
        } else {
            $this->radioDevice->enable();

        }
        echo (get_class($this->radioDevice) . " toggle power " . ($this->radioDevice->isEnabled() ? "On" : "Off"));
    }
    public function volumeUp(): void
    {
        echo (get_class($this->radioDevice) . " volume up");
        $currentVolume = $this->radioDevice->getVolume();
        $this->radioDevice->setVolume($currentVolume + 10);

    }
    public function volumeDown(): void
    {
        echo (get_class($this->radioDevice) . " volume down");
        $currentVolume = $this->radioDevice->getVolume();
        $this->radioDevice->setVolume($currentVolume - 10);

    }
    public function channelDown(): void
    {
        echo (get_class($this->radioDevice) . " channel down");
        $currentChannel = $this->radioDevice->getChannel();
        $this->radioDevice->setChannel($currentChannel + 1);
    }
    public function channelUp(): void
    {
        echo (get_class($this->radioDevice) . " channel up");
        $currentChannel = $this->radioDevice->getChannel();
        $this->radioDevice->setChannel($currentChannel - 1);
    }
}
class AdvancedRemoteController extends RemoteController implements AdvancedRemoteControlInterface
{
    public function __construct(private DeviceInterface $radioDevice)
    {

    }
    public function mute(): void
    {
        echo ("AdvancedRemoteController " . get_class($this->radioDevice) . " mute");
        $this->radioDevice->setVolume(0);
    }
}

class Radio implements DeviceInterface
{
    private $maxChannels = 105;
    private $isEnabled = false;
    private $volume = 50;
    private $channel_no = 1;
    public function __construct()
    {

    }
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }
    public function enable(): void
    {
        $this->isEnabled = true;
    }
    public function disable(): void
    {
        $this->isEnabled = false;
    }
    public function getVolume(): int
    {
        return $this->volume;
    }
    public function setVolume(int $level): void
    {
        if ($level >= 0 && $level <= 100) {
            $this->volume = $level;
        }
    }
    public function getChannel(): int
    {
        return $this->channel_no;
    }
    public function setChannel(int $channel_no): void
    {
        if ($channel_no >= 0 && $channel_no <= $this->maxChannels) {
            $this->channel_no = $channel_no;
        }
    }
}
class TV implements DeviceInterface
{
    private $maxChannels = 120;
    private $isEnabled = false;
    private $volume = 50;
    private $channel_no = 1;
    public function __construct()
    {

    }
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }
    public function enable(): void
    {
        $this->isEnabled = true;
    }
    public function disable(): void
    {
        $this->isEnabled = false;
    }
    public function getVolume(): int
    {
        return $this->volume;
    }
    public function setVolume(int $level): void
    {
        if ($level >= 0 && $level <= 100) {
            $this->volume = $level;
        }
    }
    public function getChannel(): int
    {
        return $this->channel_no;
    }
    public function setChannel(int $channel_no): void
    {
        if ($channel_no >= 0 && $channel_no <= $this->maxChannels) {
            $this->channel_no = $channel_no;
        }
    }
}



echo "<pre>";

// control Radio from remote controller
(new RemoteController(new Radio))->togglePower();
echo "\n";
(new RemoteController(new Radio))->volumeUp();
echo "\n";


// control TV from same controller
(new RemoteController(new TV))->togglePower();
echo "\n";
(new RemoteController(new TV))->volumeDown();
echo "\n";

// control TV from advanced controller
(new AdvancedRemoteController(new TV))->mute();
echo "\n";

echo "</pre>";