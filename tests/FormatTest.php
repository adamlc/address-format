<?php

use Adamlc\AddressFormat\Format;

class FormatTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Adamlc\AddressFormat\Format
     */
    protected $container;

    /**
     * Setup procedure which runs before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->container = new Format;
    }

    /**
     * Test setting a locale
     *
     * @return void
     */
    public function testSettingLocale()
    {
        $this->assertTrue(
            $this->container->setLocale('GB')
        );
    }

    /**
     * Test setting an invalid locale
     *
     * @return void
     */
    public function testSettingInvalidLocale()
    {
        $this->expectException(Adamlc\AddressFormat\Exceptions\LocaleNotSupportedException::class);
        $this->container->setLocale('FOO');
    }

    /**
     * Test setting an invalid locale
     *
     * @return void
     */
    public function testLocaleWithInvalidMetaData()
    {
        $this->expectException(Adamlc\AddressFormat\Exceptions\LocaleParseErrorException::class);
        $this->container->setLocale('Test');
    }

    /**
     * Test setting a valid attribute
     *
     * @return void
     */
    public function testSetAttributeWithValidAttribute()
    {
		$this->assertEquals(
			$this->container->setAttribute('ADMIN_AREA', 'Foo Land'),
			'Foo Land'
		);
    }

    /**
     * Test setting an invalid attribute
     *
     * @return void
     */
    public function testSetAttributeWithInvalidAttribute()
    {
        $this->expectException(Adamlc\AddressFormat\Exceptions\AttributeInvalidException::class);
        $this->container->setAttribute('PLACE_OF_FOO', 'Foo Land');
    }

    /**
     * Test getting a valid attribute
     *
     * @return void
     */
    public function testGetAttributeWithValidAttribute()
    {
    	$this->container->setAttribute('ADMIN_AREA', 'Foo Land');

		$this->assertEquals(
			$this->container->getAttribute('ADMIN_AREA'),
			'Foo Land'
		);
    }

    /**
     * Test getting an invalid attribute
     *
     * @return void
     */
    public function testGetAttributeWithInvalidAttribute()
    {
        $this->expectException(Adamlc\AddressFormat\Exceptions\AttributeInvalidException::class);
        $this->container->getAttribute('PLACE_OF_FOO');
    }

    /**
     * Check the format of a GB address is expected
     *
     * @return void
     */
    public function testGbAddressFormat()
    {
    	//Clear any previously set attributes
    	$this->container->clearAttributes();

    	//Set Locale and attributes
		$this->container->setLocale('GB');

		$this->container->setAttribute('ADMIN_AREA', 'London');
		$this->container->setAttribute('LOCALITY', 'Greenwich');
		$this->container->setAttribute('RECIPIENT', 'Joe Bloggs');
		$this->container->setAttribute('ORGANIZATION', 'Novotel London');
		$this->container->setAttribute('POSTAL_CODE', 'SE10 8JA');
		$this->container->setAttribute('STREET_ADDRESS', '173-185 Greenwich High Road');
		$this->container->setAttribute('COUNTRY', 'United Kingdom');

		$this->assertEquals(
			$this->container->formatAddress(),
			"Joe Bloggs\nNovotel London\n173-185 Greenwich High Road\nGreenwich\nSE10 8JA"
		);
    }

    /**
     * Check the format of a DE address is expected
     *
     * @return void
     */
    public function testDeAddressFormat()
    {
    	//Clear any previously set attributes
    	$this->container->clearAttributes();

    	//Set Locale and attributes
		$this->container->setLocale('DE');

		$this->container->setAttribute('LOCALITY', 'Oyenhausen');
		$this->container->setAttribute('RECIPIENT', 'Eberhard Wellhausen');
		$this->container->setAttribute('ORGANIZATION', 'Wittekindshof');
		$this->container->setAttribute('POSTAL_CODE', '32547');
		$this->container->setAttribute('STREET_ADDRESS', 'Schulstrasse 4');

		$this->assertEquals(
			$this->container->formatAddress(),
			"Eberhard Wellhausen\nWittekindshof\nSchulstrasse 4\n32547 Oyenhausen"
		);
    }

    /**
     * Check the format of a DE address is expected even when missing attributes
     *
     * @return void
     */
    public function testDeAddressFormatWithMissingAttributes()
    {
    	//Clear any previously set attributes
    	$this->container->clearAttributes();

    	//Set Locale and attributes
	$this->container->setLocale('DE');

	$this->container->setAttribute('LOCALITY', 'Oyenhausen');
	$this->container->setAttribute('RECIPIENT', '');
	$this->container->setAttribute('ORGANIZATION', '');
	$this->container->setAttribute('POSTAL_CODE', '32547');
	$this->container->setAttribute('STREET_ADDRESS', 'Schulstrasse 4');

	$this->assertEquals(
		$this->container->formatAddress(), "Schulstrasse 4\n32547 Oyenhausen"
	);
    }

    /**
     * Check that an exception is thrown for invlidate locale
     *
     * @return void
     */
    public function testUnsupportedLocaleThrowsException()
    {
        $this->expectException(Adamlc\AddressFormat\Exceptions\LocaleNotSupportedException::class);
        //Clear any previously set attributes
        $this->container->clearAttributes();

        //Set Locale
        $this->container->setLocale('XX');

        //Set expected Exception
        $this->setExpectedException('Adamlc\AddressFormat\Exceptions\LocaleNotSupportedException');

        $this->container->formatAddress();
    }

    /**
     * Check that an exception is thrown for invlidate locale
     *
     * @return void
     */
    public function testNotGivenFormatThrowsException()
    {
        //Clear any previously set attributes
        $this->container->clearAttributes();

        //Set expected Exception
        $this->expectException(Adamlc\AddressFormat\Exceptions\LocaleMissingFormatException::class);

        $this->container->formatAddress();
    }

    /**
     * Test setting attributes using array access
     *
     * @return void
     */
    public function testArrayAccess()
    {
    	//Clear any previously set attributes
    	$this->container->clearAttributes();

		$this->container['LOCALITY'] = 'Oyenhausen';
		$this->container['RECIPIENT'] = 'Eberhard Wellhausen';
		$this->container['ORGANIZATION'] = 'Wittekindshof';
		$this->container['POSTAL_CODE'] = '32547';
		$this->container['STREET_ADDRESS'] = 'Schulstrasse 4';

		$this->assertEquals(
			$this->container['LOCALITY'],
			'Oyenhausen'
		);

		$this->assertEquals(
			$this->container['RECIPIENT'],
			'Eberhard Wellhausen'
		);

		$this->assertEquals(
			$this->container['ORGANIZATION'],
			'Wittekindshof'
		);

		$this->assertEquals(
			$this->container['POSTAL_CODE'],
			'32547'
		);

		$this->assertEquals(
			$this->container['STREET_ADDRESS'],
			'Schulstrasse 4'
		);
    }

    /**
    * Check that an exception is thrown for validAddressPieces by invlidate locale
    *
    * @return void
    */
    public function testValidAddressPiecesLocaleMissingFormatException()
    {
        //Clear any previously set attributes
        $this->container->clearAttributes();

        $this->expectException(Adamlc\AddressFormat\Exceptions\LocaleMissingFormatException::class);

        $this->container->validAddressPieces();
    }

    /**
     * Test get the ordered adress pieces for this locale
     *
     * @return void
     */
    public function testValidAddressPieces()
    {
    	//Clear any previously set attributes
    	$this->container->clearAttributes();

        //Set Locale
	$this->container->setLocale('DE');

	//get the ordered adress pieces for this locale
	$validAddressPieces = $this->container->validAddressPieces();

	$this->assertEquals(
		$validAddressPieces[0],
		"RECIPIENT"
	);

	$this->assertEquals(
		$validAddressPieces[1],
		"ORGANIZATION"
	);

	$this->assertEquals(
		$validAddressPieces[2],
		"STREET_ADDRESS"
	);

	$this->assertEquals(
		$validAddressPieces[3],
		"POSTAL_CODE"
	);

	$this->assertEquals(
		$validAddressPieces[4],
		"LOCALITY"
	);
    }
}
