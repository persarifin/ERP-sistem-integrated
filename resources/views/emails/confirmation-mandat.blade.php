<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Email Confirmation</title>
   </head>
   <body>
      <div style="background-color:#ffffff;margin:0;padding:0;width:100%!important;font-family:Arial,Helvetica,sans-serif">
         <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody>
               <tr>
                  <td>
                     <div align="center">
                        <table width="600" border="0" cellspacing="0" cellpadding="0" style="min-width:600px">
                           <tbody>
                              {{-- <tr>
                                 <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                       <tbody>
                                          <tr>
                                             <td valign="top" align="center" style="padding:40px 20px 0px 20px"><img src="https://ci5.googleusercontent.com/proxy/SFhy1X1Cr0dNizAxlaEOEReQdyjZp0qhSE31gOj9kyqh8AMKbyRXlAryO5PVdu5z1tnVwCS98RCfEInMXJFbf2cBB4_8IMImCajZw3x8FRWHCF6UZ-T_SJZ_1eVRuprBRSj2zy64Ykjag4H5kTfx4DMP623rG8vxb3PDIA=s0-d-e1-ft#https://image.direct.account.sony.com/lib/fe8c127477610c7f7d/m/1/30523e30-332c-4adb-ac05-379f4a812cb8.jpg" width="360" height="96" alt="PlayStation" style="display:block" border="0" class="CToWUd"></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr> --}}
                              <tr>
                                 <td valign="top" align="left">
                                    <table cellpadding="0" cellspacing="0" width="100%" style="min-width:100%">
                                       <tbody>
                                          <tr>
                                             <td>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                   <tbody>
                                                      <tr>
                                                         <td valign="top" align="center" style="font-family:Helvetica,sans-serif;font-size:24px;line-height:150%;color:#505050;padding:40px 20px 0px 20px">{!!$details["content"]!!}</td>
                                                      </tr>
                                                      @if(isset($details['buttonText']))                                                             
                                                      <tr>
                                                         <td valign="top" align="center" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050;padding:20px 0px 20px 0px">
                                                            <table width="200" border="0" cellspacing="0" cellpadding="0">
                                                               <tbody>
                                                                  <tr>
                                                                     <td align="center" height="50" style="font-family:Helvetica,sans-serif;font-size:18px;color:#ffffff; background-color: #27ae60;"> <a href="{{$details['redirect_url']}}" style="font-family:Helvetica,sans-serif;font-size:18px;color:#ffffff;text-decoration:none" target="_blank">{{$details['buttonText']}}</a> </td>
                                                                  </tr>
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr>
                                                      @endif
                                                      <tr>
                                                         <td valign="top" align="left">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                               <tbody>
                                                                  <tr>
                                                                     <td height="1" style="border:none;border-bottom:1px solid #d2d2d2;font-size:1px;line-height:1px">&nbsp;</td>
                                                                  </tr>
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr>
                                                      {{-- <tr>
                                                         <td valign="top" align="left" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050;padding:20px 20px 0px 20px">This is an important step in setting up your new account because it allows you to:</td>
                                                      </tr> --}}
                                                      {{-- <tr>
                                                         <td valign="top" align="left" style="padding:20px 40px 20px 40px">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                               <tbody>
                                                                  <tr>
                                                                     <td valign="top" align="left" width="3%" style="color:#505050;font-weight:bold">•&nbsp;</td>
                                                                     <td width="97%" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050"> Access the full range of PlayStation®Network services. </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td valign="top" align="left" width="3%" style="color:#505050;font-weight:bold">•&nbsp;</td>
                                                                     <td width="97%" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050">Verify that the email address does not belong to someone else.</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td valign="top" align="left" width="3%" style="color:#505050;font-weight:bold">•&nbsp;</td>
                                                                     <td width="97%" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050">Receive <span class="il">confirmations</span> when funds are added to your wallet and purchases are made.</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td valign="top" align="left" width="3%" style="color:#505050;font-weight:bold">•&nbsp;</td>
                                                                     <td width="97%" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050">Reset your password if you ever forget it.</td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td valign="top" align="left" width="3%" style="color:#505050;font-weight:bold">•&nbsp;</td>
                                                                     <td width="97%" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050">Receive news, special offers and other information when you're opted-in for Marketing.</td>
                                                                  </tr>
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr> --}}
                                                      {{-- <tr>
                                                         <td valign="top" align="left" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050;padding:0px 20px 0px 20px"> You can review or update your registration details by signing in to your account on a PlayStation® system or by visiting: </td>
                                                      </tr>
                                                      <tr>
                                                         <td valign="top" align="left" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050;padding:0px 20px 0px 20px"> <a href="https://cl.s6.exct.net/?qs=39ade19d2f13ba6affacb2cd4fe4466dd4236bfe6afeaf718622ccaa8e7d38c606e9bdbba9fa09c5e74f5db56ae6bc79c96bf895facd6a12" style="text-decoration:underline;color:#006db4" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://cl.s6.exct.net/?qs%3D39ade19d2f13ba6affacb2cd4fe4466dd4236bfe6afeaf718622ccaa8e7d38c606e9bdbba9fa09c5e74f5db56ae6bc79c96bf895facd6a12&amp;source=gmail&amp;ust=1612109293462000&amp;usg=AFQjCNGeLXTw865tRkrX_BbxwToK_NOsNA">https://account.<wbr>sonyentertainmentnetwork.com</a> </td>
                                                      </tr>
                                                      <tr>
                                                         <td valign="top" align="left" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050;padding:20px 20px 20px 20px"> You will need to enter your Sign-In ID (email address) and Password whenever you use your account.<br><br>  If you did not intend to register an account, someone may have registered with your information by mistake. Please contact the Customer Services Center for further assistance.  <br>  <a href="https://cl.s6.exct.net/?qs=d963d523ed26d2cabb08670044af872ef340264f7981a54c05768bb3fa27bf718f5031a927d6cb98dc3cd0125b057998e82f910ee5c400c7" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#006db4" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://cl.s6.exct.net/?qs%3Dd963d523ed26d2cabb08670044af872ef340264f7981a54c05768bb3fa27bf718f5031a927d6cb98dc3cd0125b057998e82f910ee5c400c7&amp;source=gmail&amp;ust=1612109293462000&amp;usg=AFQjCNGE3q_Lep6RgORBvHfpPsIX1ILFbg">http://asia.playstation.com/<wbr>id/en/support/</a>  </td>
                                                      </tr> --}}
                                                      {{-- <tr>
                                                         <td valign="top" align="left">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                               <tbody>
                                                                  <tr>
                                                                     <td height="1" style="border:none;border-bottom:1px solid #d2d2d2;font-size:1px;line-height:1px">&nbsp;</td>
                                                                  </tr>
                                                               </tbody>
                                                            </table>
                                                         </td>
                                                      </tr> --}}
                                                   </tbody>
                                                </table>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              {{-- <tr>
                                 <td valign="top" align="left">
                                    <table cellpadding="0" cellspacing="0" width="100%" style="min-width:100%">
                                       <tbody>
                                          <tr>
                                             <td>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                   <tbody>
                                                      <tr>
                                                         <td valign="top" align="left" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#505050;padding:20px 20px 40px 20px"> Sony Interactive Entertainment Inc. <br> <br> This e-mail message has been delivered from a send-only address. Please do not reply to this message. If you have any questions, please contact our Customer Services Center by visiting:  <br> <a href="https://cl.s6.exct.net/?qs=d963d523ed26d2ca1e877f1986940829876f04a8d6662c9f720a2b00a07230a5268475087ee40089ff8e4b6616eaa0bedabd22855a03ea38" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#006db4" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://cl.s6.exct.net/?qs%3Dd963d523ed26d2ca1e877f1986940829876f04a8d6662c9f720a2b00a07230a5268475087ee40089ff8e4b6616eaa0bedabd22855a03ea38&amp;source=gmail&amp;ust=1612109293462000&amp;usg=AFQjCNGP_mAlnucJOu5iF7DvEBZEzk1-ZQ">http://asia.playstation.com/<wbr>en-id/support/</a>  <br> <br> For Terms of Service/User Agreement and Privacy Policy, please visit:<br>  
                                                            <a href="https://cl.s6.exct.net/?qs=de752bfa7ff4671cf55640495a3c26042c7c1716349246d9851e6c060207ae2a3b693218beacb3bba43a66ee59551fca56a3794d18bdaa4b" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#006db4" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://cl.s6.exct.net/?qs%3Dde752bfa7ff4671cf55640495a3c26042c7c1716349246d9851e6c060207ae2a3b693218beacb3bba43a66ee59551fca56a3794d18bdaa4b&amp;source=gmail&amp;ust=1612109293462000&amp;usg=AFQjCNH9EbmxAbQyhoB2lyhRKBuZWCliQA">https://asia.playstation.com/<wbr>en-id/about/terms-and-<wbr>conditions/</a><br>  <br> For PlayStation® updates, please visit our official website:  <br> <a href="https://cl.s6.exct.net/?qs=75494f329d2b57d1dec8a976acb12b1b11f6df7e511190ed13814f8dd9efc6d12943828f418d3e67a915c5e8f5e61cf94d5848184ab2ef13" style="font-family:Helvetica,sans-serif;font-size:13px;line-height:150%;color:#006db4" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://cl.s6.exct.net/?qs%3D75494f329d2b57d1dec8a976acb12b1b11f6df7e511190ed13814f8dd9efc6d12943828f418d3e67a915c5e8f5e61cf94d5848184ab2ef13&amp;source=gmail&amp;ust=1612109293462000&amp;usg=AFQjCNFfbbCroh9cSQfTuJRXXOq2OSATHQ">http://asia.playstation.com/<wbr>en-id/</a>  <br> 
                                                            <br> Sony Interactive Entertainment or PlayStation™Network (PSN) does not contact customers for their sign-in IDs, passwords or personal information. If you are uncertain about any "official" message or emails you have received, please do not reply that message or email, and report to our Customer Service immediately.<br> <br> <img src="https://ci3.googleusercontent.com/proxy/igcjiiQJC8IomDcI94BBmUVrm3uETjkdM2iqzlKQl7YFMrZFdkytGHWZoSDeCZrlm6BDzMAlzgW7c69Im0Qv2sjAcY3lTz9PeIXLTMOPzcssjkWK7HnVy3pEUo_miifNO2veVvy41rKtiJ9UcRNxjBA6h0hwoxcj7KUvXw=s0-d-e1-ft#https://image.direct.account.sony.com/lib/fe8c127477610c7f7d/m/1/0a58f142-17fc-48c9-91a7-514938c5af71.png" alt="Playstation" height="15" width="20" style="display:inline-block" class="CToWUd"> and "PlayStation" are registered trademarks or trademarks of Sony Interactive Entertainment Inc. © 2020 Sony Interactive Entertainment LLC.<br> <br> <br> 
                                                         </td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr> --}}
                           </tbody>
                        </table>
                     </div>
                  </td>
               </tr>
            </tbody>
         </table>
         {{-- <img src="https://ci3.googleusercontent.com/proxy/_KYPgrIA3jh1zHsRwvl5CpNjc_QN2SaDFEdCzH4FNPjbkxhM1EgU6bEQhq0-9k7N15Tmrrj_F-u8oQM9hRbbff76AZcdlfAyQSFEyuXoQ5bAv75Oba1FPMm5W5qGd_TKH8UBCwyQCPcU4mmstQRXTrYmIoQ2P7JPUk-K5bPSJAtekqmhgk8-oARqucDFUEgzDNwbgdBQo_9YSKQs_lDaVr9RuNclnQZtNOV47-c=s0-d-e1-ft#https://cl.S6.exct.net/open.aspx?ffcb10-fec9107077620675-fe1c1272746d0c7f7c1278-fe9012747462077576-ff951579-fe2b12717763077e761778-fef5177970650c&amp;d=60135" width="1" height="1" class="CToWUd"><u></u>
         <div class="yj6qo"></div>
         <div class="adL">  
         </div> --}}
      </div>
   </body>
</html>