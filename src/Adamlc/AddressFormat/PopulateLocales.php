<?php

namespace Adamlc\AddressFormat;

/**
 * The class used to update the data we fetch from the Google API. You shouldn't need to run this.
 */
class PopulateLocales
{
    /**
     * Locale Data URL - this is used to parse the list of available countries
     *
     * (default value: 'http://i18napis.appspot.com/address/data')
     *
     * @var string
     * @access private
     */
    private $locale_data_url = 'http://i18napis.appspot.com/address/data';

    /**
     * Function to fetch data from Google API and populate local files.
     *
     * @access public
     * @return void
     */
    public function fetchData()
    {
        $locales = json_decode(file_get_contents($this->locale_data_url));

        if (isset($locales->countries)) {
            //For some reason the countries are seperated by a tilde
            $countries = explode('~', $locales->countries);

            $data_dir = __DIR__ . '/i18n';

            //Loop countries and grab the corrosponding json data
            foreach ($countries as $country) {
                $file = $data_dir . '/' . $country . '.json';

                file_put_contents($file, file_get_contents($this->locale_data_url . '/' . $country));
            }
        }
    }
}
