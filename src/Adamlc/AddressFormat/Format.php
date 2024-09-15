<?php

namespace Adamlc\AddressFormat;

use Adamlc\AddressFormat\Exceptions\AttributeInvalidException;
use Adamlc\AddressFormat\Exceptions\LocaleNotSupportedException;
use Adamlc\AddressFormat\Exceptions\LocaleParseErrorException;
use Adamlc\AddressFormat\Exceptions\LocaleMissingFormatException;

/**
 * Use this call to format a street address according to different locales
 */
class Format implements \ArrayAccess
{
    protected $locale;

    /**
     * This map specifies the content on how to format the address
     * See this URL for origin reference
     *
     * https://code.google.com/p/libaddressinput/source/browse/trunk
     * /src/com/android/i18n/addressinput/AddressField.java?r=111
     *
     * @var mixed
     * @access private
     */
    private $address_map = array(
        'S' => 'ADMIN_AREA', //state
        'C' => 'LOCALITY', //city
        'N' => 'RECIPIENT', //name
        'O' => 'ORGANIZATION', //organization
        'D' => 'DEPENDENT_LOCALITY',
        'Z' => 'POSTAL_CODE',
        'X' => 'SORTING_CODE',
        'A' => 'STREET_ADDRESS',
        'R' => 'COUNTRY'
    );

    /**
     * The input map will hold all the information we put in to the class
     *
     * @var mixed
     * @access private
     */
    private $input_map = array(
        'ADMIN_AREA' => '', //state
        'LOCALITY' => '', //city
        'RECIPIENT' => '', //name
        'ORGANIZATION' => '', //organization
        'ADDRESS_LINE_1' => '', //street1
        'ADDRESS_LINE_2' => '', //street1
        'DEPENDENT_LOCALITY' => '',
        'POSTAL_CODE' => '',
        'SORTING_CODE' => '',
        'STREET_ADDRESS' => '', //Deprecated
        'COUNTRY' => ''
    );

    /**
     * setLocale will set the locale. This is currently a 2 digit ISO country code
     *
     * @access public
     * @param  mixed $locale
     * @return boolean
     */
    public function setLocale($locale)
    {
        //Check if we have information for this locale
        $file = __DIR__ . '/i18n/' . $locale . '.json';
        if (file_exists($file)) {
            //Read the locale information from the file
            $meta = json_decode(file_get_contents($file), true);
            if (is_array($meta)) {
                $this->locale = $meta;

                return true;
            } else {
                throw new LocaleParseErrorException('Locale "' . $locale . '" could not be parsed correctly');
            }
        } else {
            throw new LocaleNotSupportedException('Locale not supported by this library');
        }
    }

    /**
     * Return the formatted address, using the locale set. Optionally return HTML or plain text
     *
     * @access public
     * @param  bool $html (default: false)
     * @return string $formatted_address
     */
    public function formatAddress($html = false)
    {
        //Check if this locale has a fmt field
        if (isset($this->locale['fmt'])) {
            $address_format = $this->locale['fmt'];

            //Loop through each address part and process it!
            $formatted_address = $address_format;

            //Replace the street values
            foreach ($this->address_map as $key => $value) {
                $replacement = empty($this->input_map[$value]) ? '' : $this->input_map[$value];
                $formatted_address = str_replace('%' . $key, $replacement, $formatted_address);
            }

            //Remove blank lines from the resulting address
            $formatted_address = preg_replace('((\%n)+)', '%n', $formatted_address);

            //Remove %n in front and back of string
            $formatted_address = trim($formatted_address, '%n');

            //Replace new lines!
            if ($html) {
                $formatted_address = htmlentities($formatted_address, ENT_QUOTES, 'UTF-8', false);
                $formatted_address = str_replace('%n', "\n" . '<br>', $formatted_address);
            } else {
                $formatted_address = trim(str_replace('%n', "\n", $formatted_address));
            }

            return $formatted_address;
        } else {
            throw new LocaleMissingFormatException('Locale missing format');
        }
    }

    /**
     * Set an address attribute.
     *
     * @access public
     * @param  mixed  $attribute
     * @param  mixed  $value
     * @return string $value
     */
    public function setAttribute($attribute, $value)
    {
        //Check this attribute is support
        if (isset($this->input_map[$attribute])) {
            $this->input_map[$attribute] = $value;

            return $value;
        } else {
            throw new AttributeInvalidException('Attribute not supported by this library');
        }
    }

    /**
     * Get an address attribute.
     *
     * @access public
     * @param  mixed $attribute
     * @return string
     */
    public function getAttribute($attribute)
    {
        //Check this attribute is support
        if (isset($this->input_map[$attribute])) {
            return $this->input_map[$attribute];
        } else {
            throw new AttributeInvalidException('Attribute not supported by this library');
        }
    }

    /**
     * Clear all attributes.
     *
     * @access public
     * @return void
     */
    public function clearAttributes()
    {
        foreach ($this->input_map as $key => $value) {
            $this->input_map[$key] = '';
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->input_map[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->offsetSet($offset, '');
        }
    }

    /**
     * Return the valid pieces
     *
     * @access public
     * @return array
     */
    public function validAddressPieces()
    {
        $return = array();

        if (isset($this->locale['fmt']))
        {
            $address_format_array = explode("%", $this->locale['fmt']);
            foreach($address_format_array as $key => $value )
            {
                $value = trim($value);
                if( !empty($value) && isset($this->address_map[$value]) )
                {
                    $return[]=$this->address_map[$value];
                }
            }
            return $return;
        } else {
            throw new LocaleMissingFormatException('Locale missing format');
        }
    }
}
