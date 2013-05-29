<?php
/**
 * OpenIDProvider.php -- Class referring to an individual OpenID provider
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @file
 * @ingroup Extensions
 */
class OpenIDProvider {
	/**
	 * Properties about this provider
	 * @var string
	 */
	protected $providerName, $label, $url;

	public function __construct( $providerName, $label, $url ) {
		$this->providerName = $providerName;
		$this->label = $label;
		$this->url = $url;
	}

	/**
	 * Get the HTML for the OpenID provider buttons
	 * @param $classSize String Size for the openid_ class, either large or small
	 * @return string
	 */
	public function getButtonHTML( $classSize ) {
		global $wgOpenIDShowProviderIcons;

		if ( $wgOpenIDShowProviderIcons ) {
			return Html::element( 'a',
				array(
					'id' => 'openid_provider_' . $this->providerName . '_icon',
					'title' => $this->providerName,
					'href' => 'javascript:openid.show(\'' . $this->providerName . '\');',
					'class' => 'openid_' . $classSize . '_btn' . ( $this->providerName == 'openid' ? ' openid_selected' : '' )
				)
			);
		} else {
			return Html::element( 'a',
				array(
					'id' => 'openid_provider_' . $this->providerName . '_link',
					'title' => $this->providerName,
					'href' => 'javascript:openid.show(\'' . $this->providerName . '\');',
					'class' => 'openid_' . $classSize . '_link' . ( $this->providerName == 'openid' ? ' openid_selected' : '' )
				),
				$this->providerName
			);
		}

	}

	/**
	 * @return string
	 */
	public function getLoginFormHTML() {

		if ( $this->providerName == 'OpenID' ) {

			global $wgRequest;
			$inputHtml = Html::element( 'input',
				array(
					'type' => 'text',
					'name' => 'openid_url',
					'id' => 'openid_url',
					'size' => '45',
					'value' => htmlspecialchars( $wgRequest->getCookie( 'OpenID', null, '' ) )
				)
			);

		} else {

			$inputHtml = Html::element( 'input',
				array(
					'type' => 'hidden',
					'id' => 'openid_provider_url_' . $this->providerName,
					'value' => $this->url
				)
			);

			$inputHtml .= Html::element( 'input',
				array(
					'type' => ( strpos( $this->url, '{' ) === false ) ? 'hidden' : 'text',
					'id' => 'openid_provider_param_' . $this->providerName,
					'size' => '25',
					'value' => ''
				)
			);

		}

		$html = Html::rawElement( 'div',
			array(
				'id' => 'provider_form_' . $this->providerName,
				'style' => ( $this->providerName == 'openid' ) ? 'display=inline-block' : 'display:none',
			),
			Html::rawElement( 'div',
				array(),
				$label = Html::element( 'label', array( 'for' => 'openid_url' ), $this->label )
			) .
			$inputHtml .
			Xml::submitButton( wfMessage( 'userlogin' )->text() )
		);

		return $html;
	}

	/**
	 * Get the list of major OpenID providers
	 * @return array of OpenIDProvider
	 */
	public static function getLargeProviders() {
		return  array(
			new self( 'OpenID',
				wfMessage( 'openid-provider-label-openid' )->text(),
				'{URL}'
			),
			new self( 'Google',
				wfMessage( 'openid-provider-label-google' )->text(),
				'https://www.google.com/accounts/o8/id'
			),
			new self( 'Yahoo',
				wfMessage( 'openid-provider-label-yahoo' )->text(),
				'http://yahoo.com/'
			),
			new self( 'AOL',
				wfMessage( 'openid-provider-label-aol' )->text(),
				'http://openid.aol.com/{username}'
			),
		);
	}

	/**
	 * Get a list of lesser-known OpenID providers
	 * @return array of OpenIDProvider
	 */
	public static function getSmallProviders() {
		return array(
			new self( 'MyOpenID',
				wfMessage( 'openid-provider-label-other-username', 'MyOpenID' )->text(),
				'http://{username}.myopenid.com/'
			),
			new self( 'LiveJournal',
				wfMessage( 'openid-provider-label-other-username', 'LiveJournal' )->text(),
				'http://{username}.livejournal.com/'
			),
			new self( 'Blogger',
				wfMessage( 'openid-provider-label-other-username', 'Blogger' )->text(),
				'http://{username}.blogspot.com/'
			),
			new self( 'Flickr',
				wfMessage( 'openid-provider-label-other-username', 'Flickr' )->text(),
				'http://flickr.com/photos/{username}/'
			),
			new self( 'Verisign',
				wfMessage( 'openid-provider-label-other-username', 'Verisign' )->text(),
				'http://{username}.pip.verisignlabs.com/'
			),
			new self( 'ClaimID',
				wfMessage( 'openid-provider-label-other-username', 'ClaimID' )->text(),
				'http://claimid.com/{username}'
			)
		);
	}
}
