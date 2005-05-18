﻿/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * File Name: diacritic.js
 * 	Scripts for the fck_universalkey.html page.
 * 
 * File Authors:
 * 		Michel Staelens (michel.staelens@wanadoo.fr)
 * 		Abdul-Aziz Al-Oraij (top7up@hotmail.com)
 */

var dia = new Array()

dia["0060"]=new Array();dia["00B4"]=new Array();dia["005E"]=new Array();dia["00A8"]=new Array();dia["007E"]=new Array();dia["00B0"]=new Array();dia["00B7"]=new Array();dia["00B8"]=new Array();dia["00AF"]=new Array();dia["02D9"]=new Array();dia["02DB"]=new Array();dia["02C7"]=new Array();dia["02D8"]=new Array();dia["02DD"]=new Array();dia["031B"]=new Array();
dia["0060"]["0061"]="00E0";dia["00B4"]["0061"]="00E1";dia["005E"]["0061"]="00E2";dia["00A8"]["0061"]="00E4";dia["007E"]["0061"]="00E3";dia["00B0"]["0061"]="00E5";dia["00AF"]["0061"]="0101";dia["02DB"]["0061"]="0105";dia["02D8"]["0061"]="0103";
dia["00B4"]["0063"]="0107";dia["005E"]["0063"]="0109";dia["00B8"]["0063"]="00E7";dia["02D9"]["0063"]="010B";dia["02C7"]["0063"]="010D";
dia["02C7"]["0064"]="010F";
dia["0060"]["0065"]="00E8";dia["00B4"]["0065"]="00E9";dia["005E"]["0065"]="00EA";dia["00A8"]["0065"]="00EB";dia["00AF"]["0065"]="0113";dia["02D9"]["0065"]="0117";dia["02DB"]["0065"]="0119";dia["02C7"]["0065"]="011B";dia["02D8"]["0065"]="0115";
dia["005E"]["0067"]="011D";dia["00B8"]["0067"]="0123";dia["02D9"]["0067"]="0121";dia["02D8"]["0067"]="011F";
dia["005E"]["0068"]="0125";
dia["0060"]["0069"]="00EC";dia["00B4"]["0069"]="00ED";dia["005E"]["0069"]="00EE";dia["00A8"]["0069"]="00EF";dia["007E"]["0069"]="0129";dia["00AF"]["0069"]="012B";dia["02DB"]["0069"]="012F";dia["02D8"]["0069"]="012D";
dia["005E"]["006A"]="0135";
dia["00B8"]["006B"]="0137";
dia["00B4"]["006C"]="013A";dia["00B7"]["006C"]="0140";dia["00B8"]["006C"]="013C";dia["02C7"]["006C"]="013E";
dia["00B4"]["006E"]="0144";dia["007E"]["006E"]="00F1";dia["00B8"]["006E"]="0146";dia["02D8"]["006E"]="0148";
dia["0060"]["006F"]="00F2";dia["00B4"]["006F"]="00F3";dia["005E"]["006F"]="00F4";dia["00A8"]["006F"]="00F6";dia["007E"]["006F"]="00F5";dia["00AF"]["006F"]="014D";dia["02D8"]["006F"]="014F";dia["02DD"]["006F"]="0151";dia["031B"]["006F"]="01A1";
dia["00B4"]["0072"]="0155";dia["00B8"]["0072"]="0157";dia["02C7"]["0072"]="0159";
dia["00B4"]["0073"]="015B";dia["005E"]["0073"]="015D";dia["00B8"]["0073"]="015F";dia["02C7"]["0073"]="0161";
dia["00B8"]["0074"]="0163";dia["02C7"]["0074"]="0165";
dia["0060"]["0075"]="00F9";dia["00B4"]["0075"]="00FA";dia["005E"]["0075"]="00FB";dia["00A8"]["0075"]="00FC";dia["007E"]["0075"]="0169";dia["00B0"]["0075"]="016F";dia["00AF"]["0075"]="016B";dia["02DB"]["0075"]="0173";dia["02D8"]["0075"]="016D";dia["02DD"]["0075"]="0171";dia["031B"]["0075"]="01B0";
dia["005E"]["0077"]="0175";
dia["00B4"]["0079"]="00FD";dia["005E"]["0079"]="0177";dia["00A8"]["0079"]="00FF";
dia["00B4"]["007A"]="017A";dia["02D9"]["007A"]="017C";dia["02C7"]["007A"]="017E";
dia["00B4"]["00E6"]="01FD";
dia["00B4"]["00F8"]="01FF";
dia["0060"]["0041"]="00C0";dia["00B4"]["0041"]="00C1";dia["005E"]["0041"]="00C2";dia["00A8"]["0041"]="00C4";dia["007E"]["0041"]="00C3";dia["00B0"]["0041"]="00C5";dia["00AF"]["0041"]="0100";dia["02DB"]["0041"]="0104";dia["02D8"]["0041"]="0102";
dia["00B4"]["0043"]="0106";dia["005E"]["0043"]="0108";dia["00B8"]["0043"]="00C7";dia["02D9"]["0043"]="010A";dia["02C7"]["0043"]="010C";
dia["02C7"]["0044"]="010E";
dia["0060"]["0045"]="00C8";dia["00B4"]["0045"]="00C9";dia["005E"]["0045"]="00CA";dia["00A8"]["0045"]="00CB";dia["00AF"]["0045"]="0112";dia["02D9"]["0045"]="0116";dia["02DB"]["0045"]="0118";dia["02C7"]["0045"]="011A";dia["02D8"]["0045"]="0114";
dia["005E"]["0047"]="011C";dia["00B8"]["0047"]="0122";dia["02D9"]["0047"]="0120";dia["02D8"]["0047"]="011E";
dia["005E"]["0048"]="0124";
dia["0060"]["0049"]="00CC";dia["00B4"]["0049"]="00CD";dia["005E"]["0049"]="00CE";dia["00A8"]["0049"]="00CF";dia["007E"]["0049"]="0128";dia["00AF"]["0049"]="012A";dia["02D9"]["0049"]="0130";dia["02DB"]["0049"]="012E";dia["02D8"]["0049"]="012C";
dia["005E"]["004A"]="0134";
dia["00B8"]["004B"]="0136";
dia["00B4"]["004C"]="0139";dia["00B7"]["004C"]="013F";dia["00B8"]["004C"]="013B";dia["02C7"]["004C"]="013D";
dia["00B4"]["004E"]="0143";dia["007E"]["004E"]="00D1";dia["00B8"]["004E"]="0145";dia["02D8"]["004E"]="0147";
dia["0060"]["004F"]="00D2";dia["00B4"]["004F"]="00D3";dia["005E"]["004F"]="00D4";dia["00A8"]["004F"]="00D6";dia["007E"]["004F"]="00D5";dia["00AF"]["004F"]="014C";dia["02D8"]["004F"]="014E";dia["02DD"]["004F"]="0150";dia["031B"]["004F"]="01A0";
dia["00B4"]["0052"]="0154";dia["00B8"]["0052"]="0156";dia["02C7"]["0052"]="0158";
dia["00B4"]["0053"]="015A";dia["005E"]["0053"]="015C";dia["00B8"]["0053"]="015E";dia["02C7"]["0053"]="0160";
dia["00B8"]["0054"]="0162";dia["02C7"]["0054"]="0164";
dia["0060"]["0055"]="00D9";dia["00B4"]["0055"]="00DA";dia["005E"]["0055"]="00DB";dia["00A8"]["0055"]="00DC";dia["007E"]["0055"]="0168";dia["00B0"]["0055"]="016E";dia["00AF"]["0055"]="016A";dia["02DB"]["0055"]="0172";dia["02D8"]["0055"]="016C";dia["02DD"]["0055"]="0170";dia["031B"]["0055"]="01AF";
dia["005E"]["0057"]="0174";
dia["00B4"]["0059"]="00DD";dia["005E"]["0059"]="0176";dia["00A8"]["0059"]="0178";
dia["00B4"]["005A"]="0179";dia["02D9"]["005A"]="017B";dia["02C7"]["005A"]="017D";
dia["00B4"]["00C6"]="01FC";
dia["00B4"]["00D8"]="01FE";