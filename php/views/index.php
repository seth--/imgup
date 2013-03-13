        <div class="container-fluid">
            <h1>imgup</h1>
            <div class="row">
                <div class ="span12" style="text-align: center; position:absolute; top:50%; width:80%;">
                    <form action='<?PHP echo $_CONTROLLER['BASE_DIR'] ?>/upload' method="post" enctype="multipart/form-data" class="form-inline js-only">
                        <div>
                          <a class="btn btn-large" onclick="$('#multifile_input').click()">Browse</a> <input type="submit" class="btn btn-large btn-primary" value="upload">
                          <input id="multifile_input" name="file[]" type="file" style="display:none" accept="gif|jpg|jpeg|png|bmp">
                        </div>
                        <div class="margin-4">
                            <label>Delete in
                              <select name="duration">
                                  <option value="900">15 minutes</option>
                                  <option value="10800">3 hours</option>
                                  <option value="86400">1 day</option>
                                  <option value="604800">1 week</option>
                                  <option value="2592000">1 monht</option>
                                  <option value="31536000">1 year</option>
                                  <option value="500000000">never</option>
                              </select>
                            </label>
                        </div>
                        <ul id="multifile_list"></ul>
                    </form>
                    <noscript>
                        <form action='<?PHP echo $_CONTROLLER['BASE_DIR'] ?>/upload' method="post" enctype="multipart/form-data" class="form-inline">
                          <div>
                            <input name="file[]" type="file" multiple>
                          </div>
                          <div class="margin-4">
                              <label>Delete in
                                  <select name="duration">
                                      <option value="900">15 minutes</option>
                                      <option value="10800">3 hours</option>
                                      <option value="86400">1 day</option>
                                      <option value="604800">1 week</option>
                                      <option value="2592000">1 monht</option>
                                      <option value="31536000">1 year</option>
                                      <option value="500000000">never</option>
                                  </select>
                              </label>
                              <input type="submit" class="btn btn-large btn-primary" value="upload">
                          </div>
                          <div class="alert alert-warning alert-simple">
                              <span class="label label-warning">Oops!</span> <span>Enable javascript to upload multiple files at once.</span>
                          </div>
                        </form>
                    </noscript>
                </div>