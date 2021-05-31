<?php
require "../../../POO/manager_cazy.class.php";
$manager_cazy = new ManagerCazy('10.1.22.207', 'cazy_7', 'glyco', 'Horror3');
if(isset($_GET['body']))
{
	include('../../../views/header.php');
	echo "<body onload='load_lien()'>";
	echo "<div class='flex p-4 w-100 overflow-auto' style='height: 100vh;'>";
	echo "<p id='add_prot_access' style='display:inline; margin-bottom:0px;'></p>";
	echo '<div id="cazy" class="d-flex flex-column" data-article="[ID]">';
}
else
{
	ob_start(); //Pour ne pas afficher le header dans la reponse
	include('../../../views/header.php');
	ob_end_clean();
}
?>
<?php
	if(!isset($_GET["NUMACCESS"]))
	{
		http_response_code(400);//Bad request
	}
	else
	{
		$pmid = $_GET["NUMACCESS"];
		#Récupération de l'id_article dans la table article de la base de données interne
		if ($pmid != '')
		{
			//$id_article_in_article = $manager->get('num_access', $pmid, 'article', 'id_article');
			$id_article_in_article = $manager->db->prepare("SELECT id_article FROM article WHERE num_access = $pmid");
			$id_article_in_article->execute();
			$id_article = $id_article_in_article->fetch();
			$id_article = $id_article['id_article'];
			echo '<form method="post" action = "#" enctype="multipart/form-data" >';
			echo "<input type='text' id= 'input_prot_access' placeholder='Please Enter prot_access' oninput= 'listen_input(this)'>";
			echo "<input type='button' id='add_button' value='Add' onclick='click_add()'>";
			echo '<table class="table table-responsive table-hover table-bordered" id="table_cazy">';
			echo "<thead><tr class='table-info'><th class='sort_column'>Num Accession</th><th class='sort_column'>Entry ID Cazy</th><th class='sort_column'>check_pmid</th><th class ='sort_column'>check_func</th><th class='sort_column'>check_pmid_func</th></tr></thead><tbody>";
			//$id_exist_table_prot_access = $manager->get('id_article', $id_article ,'prot_access_table', '*');
			$id_exist_table_prot_access = $manager->db->prepare("SELECT * FROM prot_access_table WHERE id_article = $id_article");
			$id_exist_table_prot_access->execute();
			$valid_id_article = $id_article_in_article->rowCount();
			$id_prot = $id_exist_table_prot_access->fetch();
			
		}
		if(!empty($id_prot))
		{
			//$array_entry_id = $manager_cazy->get(['cazy_7', 'cazy_7', 'extern_db'], 'entry_id', ['annotation', 'pub_annot', 'pub_document'], 'pub_db_acc', $pmid, 'annot_id', 'document_id');
			//var_dump($array_entry_id);
			//"SELECT entry_id FROM annotation INNER JOIN pub_annot ON annotation.annot_id = pub_annot.annot_id INNER JOIN extern_db.pub_document ON pub_annot.document_id = pub_document.document_id WHERE pub_db_acc = '28329766'";
			#Récupération du document_id dans la table pub_document de la base de données extern_db de CAZy
			$document_id_in_pub_document = $manager_cazy->pdo->prepare("SELECT document_id FROM extern_db.pub_document WHERE pub_db_acc = '$pmid'");
			$document_id_in_pub_document->execute();
			$document_id_find = $document_id_in_pub_document->fetch();
			$document_id = '';
			if (!empty($document_id_find))
			{
				$document_id = $document_id_find['document_id'];
			}
			$list_entry_id = [];
			
			if(!empty($document_id))
			{   #Récupération de annot_id dans la table pub_annot de la base de données cazy_7 pour le document_id récupérer avant
				$annot_id_in_pub_annot = $manager_cazy->pdo->prepare("SELECT annot_id FROM pub_annot WHERE document_id =" . $document_id);
				$annot_id_in_pub_annot->execute();
				$nb_line_annot_id_in_pub_annot = $annot_id_in_pub_annot->rowCount();
				if ($nb_line_annot_id_in_pub_annot > 1)
				{
					while($annot_id = $annot_id_in_pub_annot->fetch())
					{
						#Récupération de l'entry_id lié à l'annot_id qui est lié au document id dans la table annotation de la base de données cazy_7
						$entry_id_in_annotation = $manager_cazy->pdo->prepare("SELECT entry_id FROM annotation WHERE annot_id =" . $annot_id['annot_id']);
						$entry_id_in_annotation->execute();
						$nb_line_entry_id_in_annotation = $entry_id_in_annotation->rowCount();
						if($nb_line_entry_id_in_annotation != 0)
						{
							while($entry_id = $entry_id_in_annotation->fetch())
							{
								if(!empty($entry_id))
								{
									$list_entry_id[] = $entry_id['entry_id'];
					
								}
							}
						}
					}
				}
			}
			if(!empty($id_article))
			{
				#récupération des valeurs des 2 colonnes du tableau dans notre table prot_access_table de la bdd interne
				$request_prot_access_table = $manager->db->prepare("SELECT prot_access, entry_id_cazy FROM prot_access_table WHERE id_article = :id_article");
				$request_prot_access_table->bindValue(":id_article", $id_article);
				$request_prot_access_table->execute();
				$nb_line_request_prot_access_table = $request_prot_access_table->rowCount();
				if($nb_line_request_prot_access_table != 0)
				{
					while($info_prot_access_table = $request_prot_access_table->fetch())
					{
						$check_func = "NO";
						$check_pmid_func = 'NO';
						$fam_acc = 'No module';
						$ec_num = 'No EC';
						#Vérifie que entry_id existe dans cazy pour le numéro d'accession de la ligne
						$entry_id = $manager_cazy->get_entryid($info_prot_access_table['prot_access']);
						if (empty($info_prot_access_table['entry_id_cazy']) || $info_prot_access_table['entry_id_cazy'] != $entry_id)
						{
							if (!empty($entry_id))
							{
								#Si dans ma base il manque alors on le rajoute avec un update sur la ligne
								$update_prot_access_table = $manager->db->prepare("UPDATE prot_access_table SET entry_id_cazy = $entry_id WHERE prot_access = :prot_access");
								$update_prot_access_table->bindValue(":prot_access", $info_prot_access_table['prot_access']);
								$update_prot_access_table->execute();
							}
						}
						else
						{  

							//SELECT fam_acc FROM family INNER JOIN fam_composition


							$annot_id = [];
							$array_info_entry_func = [];
							$request_entry_func = $manager_cazy->pdo->prepare("SELECT fam_comp_id, function_id, entry_id FROM entry_func WHERE entry_id = :entry_id");
							$request_entry_func->bindValue(":entry_id", $info_prot_access_table['entry_id_cazy']);
							$request_entry_func->execute();
							$nb_line_request_entry_func = $request_entry_func->rowCount();
							//$request_entry_func->debugDumpParams();
							//echo 'coucou' . '<br>';
							if($nb_line_request_entry_func != 0)
							{
								//echo 'coucou' . $info_prot_access_table['prot_access'] . '<br>';
								while($info_entry_func = $request_entry_func->fetch())
								{

									#Récupération fam_acc dans la table family
									if(!empty($info_entry_func['fam_comp_id']))
									{
										$request_family = $manager_cazy->pdo->prepare("SELECT fam_acc FROM family WHERE fam_id = (SELECT fam_id FROM fam_composition WHERE fam_comp_id = :fam_comp_id)");
										$request_family->bindValue(":fam_comp_id", $info_entry_func['fam_comp_id']);
										$request_family->execute();
										$fam_acc_family = $request_family->fetch();
										if($fam_acc_family['fam_acc'] != '')
										{
											
											$fam_acc = $fam_acc_family['fam_acc'];
										}
										else
										{
											
											$fam_acc = 'No module';
										}
									}
									#Récupération ec_num dans la table function
									if(!empty($info_entry_func['function_id']))
									{
										$request_function = $manager_cazy->pdo->prepare("SELECT ec_num FROM function WHERE function_id = :function_id");
										$request_function->bindValue(":function_id", $info_entry_func['function_id']);
										$request_function->execute();
										$ec_num_function = $request_function->fetch();
										//echo $ec_num_function['ec_num'] . '<br>';
										if ($ec_num_function['ec_num'] != '')
										{
											$ec_num = $ec_num_function['ec_num'];
										}
										else
										{
											$ec_num = 'No EC'; 
										}
									}
									//gestion de l'information que donne le $check_func
									if($fam_acc != 'No module' AND $ec_num != 'No EC' )
									{
										if ($check_func == 'NO')
										{
											$check_func = $fam_acc . '/' . $ec_num;
										}
										else
										{
											$check_func .= '<br>' .$fam_acc . '/' . $ec_num ;
										}
									}
									else
									{
										$input_ec_num = "<input type='text' oninput='add_ec_num(this)' class='ec_num' id='" . $info_prot_access_table['prot_access'] ."' placeholder='Enter ec_num' size='10'>";
										$lien_add_func = '<a class= "lien_add_func" href="http://10.1.22.212/privatesite/add_entryfunct.cgi?entry_id=' . $info_prot_access_table['entry_id_cazy']  . '&edit=1&ec_num=" target="_blank">Add_func</a>';
										$check_func .='<br>' . $lien_add_func . ' ' . $input_ec_num;
									}      
									if (!empty($info_entry_func['entry_id']))
									{
										$array_info_entry_func[] = $info_entry_func['entry_id'];
										$array_info_entry_func = array_values(array_unique($array_info_entry_func));
										//var_dump($array_info_entry_func);
										$request_info_annotation = $manager_cazy->pdo->prepare("SELECT DISTINCT annot_id FROM annotation WHERE entry_id = :entry_id AND db_acc = :prot_access");
										$request_info_annotation->bindValue(":entry_id", $info_entry_func['entry_id']);
										$request_info_annotation->bindValue(":prot_access",$info_prot_access_table['prot_access']);
										$request_info_annotation->execute();
										$nb_line_request_annotation = $request_info_annotation->rowCount();
										//$request_info_annotation->debugDumpParams();
										if($nb_line_request_annotation >0)
										{
											while($annot_id_annotation = $request_info_annotation->fetch())
											{
												//echo $annot_id_annotation['annot_id'] . '<br>';
												$annot_id[$info_entry_func['entry_id']]=$annot_id_annotation['annot_id'];
													
												$request_pub_annot = $manager_cazy->pdo->prepare("SELECT pub_annot_id FROM pub_annot WHERE annot_id = :annot_id AND document_id = '$document_id'");
												$request_pub_annot->bindValue(":annot_id", $annot_id_annotation['annot_id']);
												$request_pub_annot->execute();
												$nb_line_request_pub_annot = $request_pub_annot->rowCount();
												if($nb_line_request_pub_annot > 0)
												{
													while($pub_annot_id = $request_pub_annot->fetch())
													{
														//echo $pub_annot_id['pub_annot_id'] . '<br>';
														#Récupération du pub_annot_id dans la table pub_func de la bdd de cazy_7 en fonction da l'annot_id lié au doument_id récupéré
														$request_pub_func = $manager_cazy->pdo->prepare("SELECT * FROM pub_func WHERE pub_annot_id = :pub_annot_id AND function_id = :function_id");
														$request_pub_func->bindValue(":pub_annot_id", $pub_annot_id['pub_annot_id']);
														$request_pub_func->bindValue(":function_id", $info_entry_func['function_id']);
														$request_pub_func->execute();
														$nb_line_pub_func = $request_pub_func-> rowCount();

														if ($nb_line_pub_func > 0)
														{
															$info_pub_func = $request_pub_func->fetch();
															if ($check_pmid_func != 'NO')
															{
																$check_pmid_func .= '<br>OK';
															}
															else
															{
																$check_pmid_func = 'OK';
															}
														}
														else
														{
															
															if ($check_pmid_func != 'NO')
															{
																if($check_func != 'NO')
																{
																	$lien_add_pmid_func = '<a href="http://10.1.22.212/privatesite/pub_func.cgi?edit=1&entry_id=' . $info_prot_access_table['entry_id_cazy'] . '" target="_blank">Add_pub_func</a>';
																	$check_pmid_func .= '<br>' . $lien_add_pmid_func;
																}
																else
																{
																	$check_pmid_func .= '<br>NO';
																}
																
															}
															else
															{
																$check_pmid_func = 'NO';
															}																																
														}

													}
												}
												
											}
										}
									}
								}
								
							} 
							else
							{
								if($fam_acc == 'No module' AND $ec_num == 'No EC' )
								{
									if(!empty($info_prot_access_table['entry_id_cazy']))
									{
										$input_ec_num = "<input type='text' oninput='add_ec_num(this)' class='ec_num' id='" . $info_prot_access_table['prot_access'] ."' placeholder='Enter ec_num' size='10'>";
										$lien_add_func = '<a class= "lien_add_func" href="http://10.1.22.212/privatesite/add_entryfunct.cgi?entry_id=' . $info_prot_access_table['entry_id_cazy']  . '&edit=1&ec_num=" target="_blank">Add_func</a>';
										$check_func = $lien_add_func . ' ' . $input_ec_num;
									}
								}
							}
								
							
							
						}
						if ($info_prot_access_table['entry_id_cazy'] AND $annot_id == [])
						{
							$request_annotation = $manager_cazy->pdo->prepare("SELECT * FROM annotation WHERE entry_id = :entry_id AND db_acc = :prot_access");
							$request_annotation->bindValue(":entry_id", $info_prot_access_table['entry_id_cazy']);
							$request_annotation->bindValue(":prot_access",$info_prot_access_table['prot_access']);
							$request_annotation->execute();
							//$request_annotation->debugDumpParams();
							$info_annot_id = $request_annotation->fetch();
							$annot_id[$info_prot_access_table['entry_id_cazy']]=$info_annot_id['annot_id'];
						}
						$entry_id_link = "<a href='http://cazy212.afmb.local/privatesite/cazy_views.cgi?intype=entry&searchterm=" . $info_prot_access_table['entry_id_cazy'] . "' target='_blank'>";
						$delete = "<input type=button id=input_" . $info_prot_access_table['prot_access'] . " value=Del onclick='click_delete(this)'>";
						if (!empty($info_prot_access_table['entry_id_cazy']))
						{
							if (in_array($info_prot_access_table['entry_id_cazy'], $list_entry_id))
							{
								

								echo "<tr id=line_" . $info_prot_access_table['prot_access'] . ">\n<td>" . $info_prot_access_table['prot_access'] . " " . $delete . "</td>\n<td>" . $entry_id_link . $info_prot_access_table['entry_id_cazy'] . "</a></td>\n<td>OK</td>\n<td>" . $check_func . "</td>\n<td>" . $check_pmid_func . "</td>\n<td></tr>\n";
							}
							else
							{
								//si j'ai le prot_access et l'entry_id mais que je n'ai pas d'article associé
								$add_article = '<a href="http://10.1.22.212/privatesite/add_pubmed.cgi?entry_id=' . $info_prot_access_table['entry_id_cazy'] . '&id=' . $pmid . '&annotid=' . $annot_id[$info_prot_access_table['entry_id_cazy']] . '" target="_blank"> ADD_num_access</a>';
								echo "<tr id=line_" . $info_prot_access_table['prot_access'] . ">\n<td>" .  $info_prot_access_table['prot_access'] . " " . $delete . "</td>\n<td>" . $entry_id_link . $info_prot_access_table['entry_id_cazy']  . "</a></td>\n<td>" . $add_article . "</td>\n<td>" . $check_func . "</td>\n<td>" . $check_pmid_func . "</td></tr>\n";
							}
						}
						else
						{
							//lien si le prot_access n'existe pas dans la base de données CAZy 
							$add_prot_access = '<a href="http://10.1.22.212/privatesite/new_entry.cgi?db_acc=' . $info_prot_access_table['prot_access'] . '&fetch=1" target="_blank">ADD db_acc</a>';
							echo "<tr id=line_" . $info_prot_access_table['prot_access'] . ">\n<td>" . $info_prot_access_table['prot_access'] . " " . $delete . "</td>\n<td>" . $add_prot_access . "</td>\n<td>" . "</td>\n<td>" . $check_func . "</td>\n<td>" . $check_pmid_func . "</td></tr>\n";
						}
						
						
					}
					echo '</tbody>';
					echo '</table>';
					echo '</form>';
				}
			}
		}
	}	
?>
<?php 
if(isset($_GET['body']) AND $_GET['body'] == '1')
{
	include($position . '/views/footer.php');//inclusion du pied de page
}
else
{
	echo '<script src="' . $position . '/tables/table_sort.js"></script>';
}   
    
?>