<?php 
namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;
use App\Models\User;
use App\Models\Alertlist;


class Alertcontrol extends BaseController
{
    public $now = "";
    public $oAlertlist = "";

	public function __construct()
    {
        $this->now = Time::now('Asia/Seoul', 'en_US');
        $this->oAlertlist = new Alertlist();
    }

    //전체보기
    //검색 조건 : 제목검색, 내용검색, 작성자검색, 게임타입별검색
    //페이징처리 : 페이지 번호에 10개씩 자르기 $query=$builder->get(10, 20);
    //정렬 조건 : 오름차순 내림차순, 필드 이름

    //조인 처리 : uid gid 랭크id 있으면 rankid
    public function getlist($glimit, $goffset){ //uid만 join


        log_message('alert', "-------Alertcontrol------------".json_encode($_POST)); 
        log_message('alert', "-------Alertcontrol------------".json_encode($_GET)); 
        log_message('alert', "-------Alertcontrol------------".json_encode($glimit)); 
        log_message('alert', "-------Alertcontrol------------".json_encode($goffset)); 
        

        $conditionarr = array();
        //$conditionarr["feild"] = "a.idx,a.nid,a.uid,a.document,a.likenum,a.regdate,b.usertype,b.email,b.name,b.nicname, c.idx as imgidx, c.basicuri as profilebasicuri, c.src as profilesrc, c.type";

  

        //게시글 고유번호
        if($_POST['myuid'] != null){
            $conditionarr["where"]["alertuid"]["logic"] = "=";
            $conditionarr["where"]["alertuid"]["value"] = $_POST['myuid'];
        }

        $conditionarr["order"]["feild"] = "regdate";
        $conditionarr["order"]["value"] = "DESC";

        $pagehandler = array();
        $pagehandler["limit"] = $glimit; //리스트 갯수
        $pagehandler["offset"] = $goffset; //리스트 위치 

        log_message('alert', "55555555555555  ".json_encode($conditionarr));

        $alertlist = $this->oAlertlist->getlist($conditionarr, $pagehandler);
	    

	    log_message('alert', json_encode($alertlist));
	    $response = $this->response->setJSON($alertlist);
        

	    return $response;
    }


    public function getcount($glimit, $goffset){

        log_message('alert', "-------Alertcontrol------------".json_encode($_POST)); 
        log_message('alert', "-------Alertcontrol------------".json_encode($_GET)); 
        log_message('alert', "-------Alertcontrol------------".json_encode($glimit)); 
        log_message('alert', "-------Alertcontrol------------".json_encode($goffset)); 
        
        $conditionarr = array();

        //유저 고유번호
        if($_POST['myuid'] != null){
            $conditionarr["where"]["alertuid"]["logic"] = "=";
            $conditionarr["where"]["alertuid"]["value"] = $_POST['myuid'];
        }

        if($_POST['click'] != null){
            $conditionarr["where"]["click"]["logic"] = "=";
            $conditionarr["where"]["click"]["value"] = $_POST['click'];
        }

        $conditionarr["order"]["feild"] = "regdate";
        $conditionarr["order"]["value"] = "DESC";

        $pagehandler = array();
        $pagehandler["limit"] = $glimit; //리스트 갯수
        $pagehandler["offset"] = $goffset; //리스트 위치 

        log_message('alert', "55555555555555  ".json_encode($conditionarr));

        $alertlist = $this->oAlertlist->getlist($conditionarr, $pagehandler);
	    

	    log_message('alert', json_encode(count($alertlist)));
	    $response = $this->response->setJSON(['count'  => count($alertlist)]);
       // $results = $this->response->setJSON(['result'  => $results, 'alertuid'  => $data['alertuid'], 'alertdocu'  => $data['alertdocu']]);


	    return $response;
    }

    //댓글 이미지 리스트 
    public function commentimglist($cid){


        $conditionarr = array();

        $conditionarr["where"]["cid"]["logic"] = "=";
        $conditionarr["where"]["cid"]["value"] = $cid; 

        $conditionarr["order"]["feild"] = "idx";
        $conditionarr["order"]["value"] = "DESC";

        $pagehandler = array();
        $pagehandler["limit"] = 0; //리스트 갯수
        $pagehandler["offset"] = 0; //리스트 위치 

        log_message('alert', "55555555555555  ".json_encode($conditionarr));

        $results = $this->oCommentImglist->getlist($conditionarr, $pagehandler);

       // log_message('alert', json_encode($results));
	    //$response = $this->response->setJSON($results);

	    return $results;
    }



    //내 게시글 보기에서 확인하는 리스트
    //댓글 대댓글 합치기, 해당 게시글 내용 합치기 
    public function commentlist_mypage($glimit, $goffset){
        log_message('alert', "-------comment------------".json_encode($_POST)); 
        log_message('alert', "-------comment------------".json_encode($_GET)); 
        log_message('alert', "-------comment------------".json_encode($glimit)); 
        log_message('alert', "-------comment------------".json_encode($goffset)); 


        //----댓글 생성
        $conditionarr = array();
        $conditionarr["feild"] = "a.idx,a.nid,a.uid,a.basicuri,a.src,a.document,a.regdate,b.usertype,b.email,b.name,b.nicname, c.maincategorey, c.subcategorey, c.title, c.document as ndocument";
        
        //유저 고유번호
        if($_POST['uid'] != null){
            $conditionarr["where"]["a.uid"]["logic"] = "=";
            $conditionarr["where"]["a.uid"]["value"] = $_POST['uid'];
        }


        $conditionarr["order"]["feild"] = "a.regdate";
        $conditionarr["order"]["value"] = "DESC";

        $pagehandler = array();
        $pagehandler["limit"] = $glimit; //리스트 갯수
        $pagehandler["offset"] = $goffset; //리스트 위치 

        log_message('alert', "55555555555555  ".json_encode($conditionarr));

        $commentlist = $this->oCommentlist->getlist_join_mypage($conditionarr, $pagehandler);
       
        log_message('alert', "commentlist-----------  ".json_encode($commentlist));  

	    $response = $this->response->setJSON($commentlist);
        
	    return $response;
    }
    
    //검색용 데이터 수집
    public function getdatasearch($condid, $condval){

        //조건이 여러개면? 
        log_message('alert', $condid);
        log_message('alert', $condval);
        //$conditionarray = array($condid => $condval);
        $conditionarray = array("condid" => $condid, "condval" => $condval);

        $limit = 0; //리스트 갯수
        $offset = 0; //리스트 위치 
        $results = $this->oNboardlist->getlistone($conditionarray, $limit, $offset);
	    
	    log_message('alert', json_encode($results));
	    $response = $this->response->setJSON($results);

	    return $response;
    }
    
    //info 검색 처리 : ntype으로 조절
    public function getdatainfoone(){
        $json=json_decode(file_get_contents('php://input'));
        $json=$json->data;

        log_message('alert', json_encode($json));

        $conditionarr = array();
        if($json->ntype == '3'){//자랑 - 랭크 아이디 조인 필수
            $conditionarr["feild"] = "a.idx,a.gid,a.rankid,a.ntype,a.title,a.document,a.regdate,a.views,b.email,b.name as bname,c.name as cname,c.info, d.score";
        }else{ //공략 및 잡담
            $conditionarr["feild"] = "a.idx,a.gid,a.rankid,a.ntype,a.title,a.document,a.regdate,a.views,b.email,b.name as bname,c.name as cname,c.info";
        }

        $conditionarr["where"]["a.idx"]["logic"] = "=";
        $conditionarr["where"]["a.idx"]["value"] = $json->idx;

        $pagehandler = array();

        //$json->ntype 으로 구분한다. 1일때는 랭크없이 join, 3일때는 rank 같이 join
        //자랑일때 수정으로 rankid를 join하지 않는다. 
        $results = $this->oNboardlist->getlistinfoone($conditionarr, $pagehandler, $json->ntype);
	    
	    log_message('alert', json_encode($results));
	    $response = $this->response->setJSON($results);

	    return $response;
    }

    //전체 갯수
    public function getdatacount(){
        $results = $this->oNboardlist->getlistcount();
	    
	   // log_message('alert', json_encode($results));
	    $response = $this->response->setJSON($results);

	    return $response; 
    }

    //이미지 글 formdata로 가져와서 저장함 //게시물 저장
    public function commentsavebox(){
        log_message('alert', json_encode("--------------------------")); 
        log_message('alert', json_encode("--------------------------")); 
        log_message('alert', json_encode($_FILES)); 
        log_message('alert', json_encode($_POST)); 
        log_message('alert', json_encode("--------------------------")); 
        log_message('alert', json_encode("--------------------------")); 


        
        //post전부 저장 
        if($_POST != null){
            $results = $this->getdatainsert($_POST);

            if($results == 1){
                //방금 insert한 id 가져오기
                $resultsid = $this->oCommentlist->getinsertid();
                log_message('alert', "댓글 응답 getincre  ".$resultsid); 
                //후에 nid 나오면 img 전부 저장 고고 
            }
        }

        if($_FILES != null){
            $fileresult = $this->imgupload($_FILES, $resultsid, $_POST['uid'], $_POST['currenttime']);
            log_message('alert', "게시판 응답 getincre  ".$fileresult); 
        }


        //알림에 저장한다. 
        //게시물 주인의 uid를 가져올 것 $_POST["nid"]
        $data=array("idx" => '0', 'nid' => $_POST["nid"], 'uid' => $_POST["uid"], 'alertuid' => $_POST["Nboardwriteuid"], 'alertdocu' => $_POST["myname"]."님이 회원님의 게시물에 댓글을 작성하였습니다. ", 'regdate' => $this->now);
        log_message('alert', "알림데이터 데이터 ".json_encode($data));
        
        $alertresult = $this->oAlertlist->getinsert($data);

    }

    //댓글 수정 부분
    public function updatebox(){
        log_message('alert', json_encode("--------------------------")); 
        log_message('alert', json_encode("--------------------------")); 
        log_message('alert', json_encode($_FILES)); 
        log_message('alert', json_encode($_POST)); 
        log_message('alert', json_encode("--------------------------")); 
        log_message('alert', json_encode("--------------------------")); 


        
        //post전부 저장 
        if($_POST != null){
            $results = $this->getdataupdate($_POST, $_FILES);
        }

        // if($_FILES != null){
        //     // $commentidx=array(
        //     //     'idx' => $_POST["cid"]
        //     // );

        //     //($_FILES, $_POST['nid'], $_POST['uid'], $_POST['maincategorey'], $_POST['subcategorey'], $_POST['currenttime']);
        //     $fileresult = $this->imgupload($_FILES, $_POST['cid'], $_POST['uid'], $_POST['currenttime']);

        //     log_message('alert', "댓글 응답 getincre  ".$fileresult); 
        // }else{
        //  //   log_message('alert', "댓글 응답 - 삭제처리한다". json_encode($_FILES)); 
        //  //   log_message('alert', "댓글 응답 - 삭제처리한다  ".$_FILES); 
        // }


        //$_POST['imgdeletelist'] // delete리스트 삭제 고고 하기 
        // if($_POST['imgdeletelist'] != ""){
        //     $this->deleteimage($_POST['imgdeletelist']);
        // }
    }

    //댓글 이미지 데이터 리스트 삭제하기 (댓글 수정할때)
    public function deleteimage($deletelist){
        log_message('alert', "deletelist  ".json_encode($deletelist));
        
        $deletearr = substr($deletelist, 1);
        $deletearr = substr($deletearr, 0, -1);
        $deletearr = str_replace(" ", "", $deletearr); 

        //deletef리스트 "17,14"이거 풀어서 어레이로 묵기 !!!
        $deletelistarr = explode(",", $deletearr);
        log_message('alert', "deletelist  ".json_encode($deletelistarr));
        
        $list = $this->oCommentImglist->getdelete("idx", $deletelistarr);
    }
    
    //데이터 추가
    public function getdatainsert($Data){

        $data=array("idx" => '0', 'nid' => $Data["nid"], 'uid' => $Data["uid"], 'basicuri' => "", 'src' => "", 'document' => $Data["document"], 'likenum' => 0, 'regdate' => $Data["currenttime"]);

        $results = $this->oCommentlist->getinsert($data);

        $results = $this->response->setJSON(['result'  => $results]);

        return $results; 
    }

    //데이터 업데이트
    public function getdataupdate($Data, $File){
        
        $fieldname=array(
            'idx' => $Data["alertidx"]
        );

      //  log_message('alert', "댓글 응답 - 삭제처리한다". json_encode($File)); 

       // if($File == null){
        //    $data=array('nid' => $Data["nid"], 'uid' => $Data["uid"], 'basicuri' => "", 'src' => "", 'document' => $Data["document"], 'regdate' => $Data["currenttime"]);
       // }else{



        $data=array('click' => $Data["clickval"]);


        $results = $this->oAlertlist->getupdate($fieldname, $data);
        
       $results = $this->response->setJSON(['result'  => $results]);
       log_message('alert', "알림 데이터_ ".json_encode($results));

        return $results; 
    }

    public function getdataupdate_each(){
        $json=json_decode(file_get_contents('php://input'));
        $json=$json->data;
        log_message('alert', "댓글 데이터_update ".json_encode($json));

        //패스워드가 있는경우 패스워드 확인작업 들어간다. 
        if($json->password != null){ //비회원
           // $json->idx 의 $json->password가 맞는지 확인한다. 
           $chkdata = $this->oCommentlist->Chkpassword($json->idx, $json->password);

           log_message('alert', "댓글 데이터_패스워드 확인 ".json_encode($chkdata)); //true false로 확인
           $results = $chkdata;

            //true 일때만 저장 처리함. false면 저장하지 않고 false를 뱃음
            if($results == true){
                $fieldname=array(
                    'idx' => $json->idx
                );
                $data=array('document' => $json->document);
        
                $results = $this->oCommentlist->getupdate($fieldname, $data);
            }

        }else{ //회원
            $fieldname=array(
                //'idx' => $json->gameplayinfo->rankid
                'idx' => $json->idx
            );
            $data=array('document' => $json->document);
    
            $results = $this->oCommentlist->getupdate($fieldname, $data);

        }
        




        $results = $this->response->setJSON(['result'  => $results]);
        log_message('alert', "댓글 데이터_ ".json_encode($results));
 
         return $results; 
     }


    //데이터 삭제
    public function getdatadelete(){

        log_message('alert', "-----getdatadelete-----".json_encode($_POST));


        //댓글 삭제
        $deletelist = array();
        $deletelist[0] = $_POST['cid'];
        //log_message('alert', "-----getdatadelete-----".json_encode($deletelist));
        $results = $this->oCommentlist->getdelete("idx", $deletelist);
        $list = $this->oCommentImglist->getdelete("cid", $deletelist); //댓글 이미지 삭제
        
        //대댓글 삭제
        $results = $this->oCommentlistNested->getdelete("cid", $deletelist);

        //대댓글 이미지 삭제
        $list = $this->oCommentnestedImglist->getdelete("cid", $deletelist);
        
        $results = $this->response->setJSON(['result'  => $results]);

        return $results; 
    }

    public function imgupload($files, $cid, $uid, $currenttime){

        // log_message('alert', json_encode($files)); 
        // log_message('alert', json_encode($files['files'])); 
        // log_message('alert', json_encode($files['files']['tmp_name'])); 
        // log_message('alert', json_encode($files['files']['name'])); 
        // log_message('alert', json_encode($files['files']['type']));  
        // log_message('alert', json_encode($files['files']['size']));  
        // log_message('alert', json_encode($files['files']['error']));  


        $oUpload = new Upload();

        $basicurl = "/image/comment/";

        //다중으로 가져오는 파일 정보 -- 파일 저장, 경로 db에 저장 
        //{"name":"img.png","type":"image\/png","tmp_name":"\/tmp\/phpvquFKq","error":0,"size":4052}
        foreach($files as $key => $value){
            log_message('alert', json_encode($value));  

            //        // var filename = joincode+uid+pid+"_"+year+month+day+getHours+getMinutes+getSeconds+getMilliseconds+".mp4";
            //nid 저장
            //파일명 생성해서 넘기기

            $filename = $oUpload->upload($value, $basicurl); //이미지 업로드

            log_message('alert', "img 경로".json_encode($filename));  

            $imgdata=array("idx" => '0', 'cid' => $cid, 'uid' => $uid, 'basicuri' => $basicurl, 'src' => $filename, 'regdate' => $currenttime);

            //이미지 경로 db 저장
            $reslut = $this->oCommentImglist->getinsert($imgdata);

            log_message('alert', "img result".json_encode($reslut));  
        }
        
    }


    //좋아요 추가
    public function likeadd($a, $b){
        log_message('alert', "likeadd".json_encode($_POST));

        $conditionarr = array();

        $conditionarr["where"]["giveuid"]["logic"] = "=";
        $conditionarr["where"]["giveuid"]["value"] = $_POST["uid"];
        $conditionarr["where"]["getcid"]["logic"] = "=";
        $conditionarr["where"]["getcid"]["value"] = $_POST["cid"];

        $pagehandler = array();
        $pagehandler["limit"] = 0; //리스트 갯수
        $pagehandler["offset"] = 0; //리스트 위치 
        $likeinfo = $this->oLikecommentlist->getlist($conditionarr, $pagehandler);
        log_message('alert', "좋아요 데이터 ".json_encode($likeinfo));



        if($_POST["liketype"] == 1){ //좋아요 클릭
            if($likeinfo[0] == ""){ //값이 없을때만 
                $data=array("idx" => '0', 'giveuid' => $_POST["uid"], 'getcid' => $_POST["cid"], 'regdate' => $this->now);
                log_message('alert', "프로필 데이터 ".json_encode($data));
        
                $results = $this->oLikecommentlist->getinsert($data);
            }else{
                $results = false;
            }


            //알림 저장하기.  - 
            $data=array("idx" => '0', 'nid' => $_POST["nid"], 'uid' => $_POST["uid"], 'alertuid' => $_POST["Commentwriteuid"], 'alertdocu' => $_POST["myname"]."님이 회원님의 댓글에 좋아요를 눌렀습니다. ", 'regdate' => $this->now);
            log_message('alert', "알림데이터 데이터 ".json_encode($data));
            
            $alertresult = $this->oAlertlist->getinsert($data);

            
        }else{ //0 좋아요 해제
            if($likeinfo[0] != ""){ //값이 있으면
                log_message('alert', "좋아요 삭제 데이터 ".json_encode($likeinfo[0]->idx));
                
                $deletearr = array();
                array_push($deletearr, $likeinfo[0]->idx);
                $results = $this->oLikecommentlist->getdelete("idx", $deletearr);
            }else{
                $results = false;
            }
        }



        $results = $this->response->setJSON(['result'  => $results]);
        return $results; 
    }
}
