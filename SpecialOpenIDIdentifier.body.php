<?php
/**
 * SpecialOpenIDIdentifier.body.php -- Special page for identifiers
 * Copyright 2013 by Ryan Lane
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @file
 * @author Ryan Lane <rlane@wikimedia.org>
 * @author Thomas Gries <mail@tgries.de>
 * @ingroup Extensions
 */

class SpecialOpenIDIdentifier extends unlistedSpecialPage {

	function __construct() {
		parent::__construct( 'OpenIDIdentifier' );
	}

	function execute( $par ) {
		$this->setHeaders();
		self::showOpenIDIdentifier( User::newFromId( $par ) );
	}

	private static function isUser( $user ) {
		return ( $user ) ? $user->loadFromId() : false;
	}

	/**
	 * @param $user User
	 * @param $delegate bool
	 */
	public static function showOpenIDIdentifier( $user, $delegate = false ) {
		global $wgOut, $wgUser, $wgOpenIDClientOnly, $wgOpenIDShowUrlOnUserPage,
			$wgOpenIDAllowServingOpenIDUserAccounts, $wgOpenIDIdentifiersURL;

		// show the own OpenID Url as a subtitle on the user page
		// but only for the user when visiting their own page
		// and when the options say so

		if ( self::isUser( $user ) ) {

			$openid = SpecialOpenID::getUserOpenIDInformation( $user );

			# Add OpenID data if its allowed
			if ( !$wgOpenIDClientOnly
				&& !( count( $openid )
					&& ( strlen( $openid[0]->uoi_openid ) != 0 )
					&& !$wgOpenIDAllowServingOpenIDUserAccounts ) ) {

				$serverTitle = SpecialPage::getTitleFor( 'OpenIDServer' );
				$serverUrl = $serverTitle->getFullURL();
				$wgOut->addLink( array( 'rel' => 'openid.server', 'href' => $serverUrl ) );
				$wgOut->addLink( array( 'rel' => 'openid2.provider', 'href' => $serverUrl ) );
				if ( $delegate ) {
					$local_identity = SpecialOpenIDServer::getLocalIdentity( $user );
					$wgOut->addLink( array( 'rel' => 'openid.delegate', 'href' => $local_identity ) );
					$wgOut->addLink( array( 'rel' => 'openid2.local_id', 'href' => $local_identity ) );
				}
				$rt = SpecialPage::getTitleFor( 'OpenIDXRDS', $user->getName() );
				$xrdsUrl = $rt->getFullURL();
				$wgOut->addMeta( 'http:X-XRDS-Location', $xrdsUrl );
				header( 'X-XRDS-Location: ' . $xrdsUrl );
			}

			if ( ( $user->getID() === $wgUser->getID() )
				&& ( $user->getID() != 0 )
				&& ( $wgOpenIDShowUrlOnUserPage === 'always'
					|| ( ( $wgOpenIDShowUrlOnUserPage === 'user' ) && !$wgUser->getOption( 'openid-hide-openid' ) ) ) ) {

				$wgOut->setSubtitle( "<span class='subpages'>" .
					OpenIDHooks::getOpenIDSmallLogoUrlImageTag() .
					SpecialOpenIDServer::getLocalIdentityLink( $wgUser ) .
					"</span>" );

				$wgOut->addWikiMsg( 'openid-identifier-page-text-user', $wgUser->getName() );

			} else {

				$wgOut->addWikiMsg( 'openid-identifier-page-text-different-user', $user->getID() );

			}

		} else {

			$wgOut->addWikiMsg( 'openid-identifier-page-text-no-such-local-openid', $user->getID() );

		}

	}

}
